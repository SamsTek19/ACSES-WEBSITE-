import React, { useEffect, useState } from 'react';
import { Link, Navigate } from 'react-router-dom';
import { ArrowLeft, CalendarDays, LoaderCircle, Send } from 'lucide-react';
import { useEvents } from '../lib/useEvents';
import { usePortal } from './PortalApp';

type WorkspaceTab = 'events' | 'announcements' | 'resources' | 'timeline' | 'suggestions' | 'elections' | 'results';

interface Announcement {
  id: string;
  title: string;
  excerpt: string | null;
  content: string | null;
  priority: string;
  published_at: string;
}

interface Resource {
  id: string;
  title: string;
  category: string;
  resource_type: string;
  content_type: string;
  description: string | null;
  cta_label: string | null;
  cta_url: string | null;
  file_path: string | null;
}

interface Election {
  id: string;
  title: string;
  description: string | null;
  start_at: string;
  end_at: string;
  status: string;
}

interface Candidate {
  id: string;
  election_id: string;
  name: string;
  position_id: string;
  short_description: string | null;
}

interface ElectionPosition {
  id: string;
  election_id: string;
  title: string;
}

interface VoterStatus {
  election_id: string;
  is_eligible: boolean;
  has_voted: boolean;
}

const tabs: { id: WorkspaceTab; label: string }[] = [
  { id: 'events', label: 'Events' },
  { id: 'announcements', label: 'Announcements' },
  { id: 'resources', label: 'Resources' },
  { id: 'timeline', label: 'Academic timeline' },
  { id: 'suggestions', label: 'Suggestions' },
  { id: 'elections', label: 'Elections' },
  { id: 'results', label: 'Election results' },
];

export const StudentWorkspace: React.FC = () => {
  const { client, session, profile, loading, configurationMissing } = usePortal();
  const { events, status: eventsStatus } = useEvents();
  const [tab, setTab] = useState<WorkspaceTab>('events');
  const [announcements, setAnnouncements] = useState<Announcement[]>([]);
  const [resources, setResources] = useState<Resource[]>([]);
  const [elections, setElections] = useState<Election[]>([]);
  const [candidates, setCandidates] = useState<Candidate[]>([]);
  const [positions, setPositions] = useState<ElectionPosition[]>([]);
  const [voterStatuses, setVoterStatuses] = useState<VoterStatus[]>([]);
  const [selectedCandidates, setSelectedCandidates] = useState<Record<string, string>>({});
  const [subject, setSubject] = useState('');
  const [message, setMessage] = useState('');
  const [notice, setNotice] = useState('');
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  useEffect(() => {
    if (!client || !profile || profile.role !== 'student') return;
    let active = true;

    void Promise.all([
      client.from('portal_announcements').select('id, title, excerpt, content, priority, published_at').order('published_at', { ascending: false }),
      client.from('portal_support_resources').select('id, title, category, resource_type, content_type, description, cta_label, cta_url, file_path').order('display_order').order('title'),
      client.from('portal_elections').select('id, title, description, start_at, end_at, status').order('start_at', { ascending: false }),
      client.from('portal_election_candidates').select('id, election_id, name, position_id, short_description'),
      client.from('portal_election_positions').select('id, election_id, title'),
      client.from('portal_election_voters').select('election_id, is_eligible, has_voted').eq('profile_id', profile.id),
    ]).then(async ([announcementResult, resourceResult, electionResult, candidateResult, positionResult, voterResult]) => {
      if (!active) return;
      const queryError = announcementResult.error
        || resourceResult.error
        || electionResult.error
        || candidateResult.error
        || positionResult.error
        || voterResult.error;
      if (queryError) {
        setError(queryError.message);
        return;
      }
      setAnnouncements((announcementResult.data ?? []) as Announcement[]);
      const resourceRows = (resourceResult.data ?? []) as Resource[];
      const signedResourceLinks = await Promise.all(resourceRows.map(async (resource) => {
        if (!resource.file_path) return null;
        const { data: signedFile, error: signedUrlError } = await client.storage
          .from('student-private')
          .createSignedUrl(resource.file_path, 600);
        if (signedUrlError) return null;
        return [resource.id, signedFile.signedUrl] as const;
      }));
      setResources(resourceRows.map((resource) => ({
        ...resource,
        cta_url: resource.cta_url ?? signedResourceLinks.find((link) => link?.[0] === resource.id)?.[1] ?? null,
        file_path: null,
      })));
      setElections((electionResult.data ?? []) as Election[]);
      setCandidates((candidateResult.data ?? []) as Candidate[]);
      setPositions((positionResult.data ?? []) as ElectionPosition[]);
      setVoterStatuses((voterResult.data ?? []) as VoterStatus[]);
    });

    return () => { active = false; };
  }, [client, profile]);

  if (configurationMissing) return <main className="p-8 text-sm text-amber-900">Configure Supabase to use the student workspace.</main>;
  if (loading) return <main className="p-8 text-sm text-slate-600">Loading account...</main>;
  if (!session) return <Navigate to="/portal/login" replace />;
  if (profile?.role !== 'student' || profile.account_status !== 'approved') return <main className="p-8 text-sm text-rose-800">Approved student access is required.</main>;
  if (!client) return null;

  const submitSuggestion = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    setBusy(true);
    setError('');
    setNotice('');
    const { error: insertError } = await client.from('portal_suggestions').insert({
      user_id: profile.id,
      category: 'general',
      subject: subject.trim(),
      message: message.trim(),
    });
    setBusy(false);
    if (insertError) {
      setError(insertError.message);
      return;
    }
    setSubject('');
    setMessage('');
    setNotice('Your message was sent to the ACSES team.');
  };

  const castVote = async (electionId: string): Promise<void> => {
    const candidateId = selectedCandidates[electionId];
    if (!candidateId) return;
    setBusy(true);
    setError('');
    const { error: voteError } = await client.rpc('cast_election_vote', {
      p_election_id: electionId,
      p_candidate_id: candidateId,
    });
    setBusy(false);
    if (voteError) {
      setError(voteError.message);
      return;
    }
    setVoterStatuses((current) => current.map((voter) => voter.election_id === electionId ? { ...voter, has_voted: true } : voter));
    setNotice('Your ballot was submitted.');
  };

  return (
    <main className="min-h-screen bg-slate-50">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6">
          <Link to="/portal" className="inline-flex items-center gap-2 text-sm font-semibold text-emerald-900"><ArrowLeft className="h-4 w-4" />Portal dashboard</Link>
          <span className="text-sm font-semibold text-slate-700">{profile.full_name}</span>
        </div>
      </header>
      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6">
        <p className="text-xs font-bold uppercase text-emerald-800">Student services</p>
        <h1 className="mt-1 text-3xl font-bold text-slate-900">Student workspace</h1>
        <div role="tablist" aria-label="Student workspace" className="mt-6 flex flex-wrap gap-2 border-b border-slate-200 pb-3">
          {tabs.map((item) => <button key={item.id} role="tab" aria-selected={tab === item.id} onClick={() => setTab(item.id)} className={`rounded-md px-3 py-2 text-sm font-semibold ${tab === item.id ? 'bg-emerald-900 text-white' : 'text-slate-600 hover:bg-white'}`}>{item.label}</button>)}
        </div>
        {error && <p role="alert" className="mt-5 rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{error}</p>}
        {notice && <p role="status" className="mt-5 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900">{notice}</p>}

        {tab === 'events' && <section className="mt-6"><h2 className="text-xl font-bold text-slate-900">Department events</h2>{eventsStatus === 'loading' ? <p className="mt-4 text-sm text-slate-500">Loading events...</p> : <div className="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">{events.map((event) => <article key={event.id} className="p-4"><div className="flex items-center gap-2 text-xs font-semibold uppercase text-emerald-800"><CalendarDays className="h-4 w-4" />{event.category}</div><h3 className="mt-2 font-semibold text-slate-900">{event.title}</h3><p className="mt-1 text-sm text-slate-600">{event.date} · {event.time} · {event.location}</p>{event.description && <p className="mt-2 text-sm leading-6 text-slate-600">{event.description}</p>}</article>)}{events.length === 0 && <p className="p-4 text-sm text-slate-500">No events published.</p>}</div>}</section>}

        {tab === 'announcements' && <section className="mt-6"><h2 className="text-xl font-bold text-slate-900">Announcements</h2><div className="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">{announcements.map((item) => <article key={item.id} className="p-4"><p className="text-xs font-semibold uppercase text-emerald-800">{item.priority}</p><h3 className="mt-1 font-semibold text-slate-900">{item.title}</h3><p className="mt-2 text-sm text-slate-600">{item.excerpt || item.content}</p></article>)}{announcements.length === 0 && <p className="p-4 text-sm text-slate-500">No announcements for your account.</p>}</div></section>}

        {tab === 'resources' && <section className="mt-6"><h2 className="text-xl font-bold text-slate-900">Study resources</h2><div className="mt-4 grid gap-3 md:grid-cols-2">{resources.map((resource) => { const href = resource.cta_url || (resource.file_path ? client.storage.from('student-private').getPublicUrl(resource.file_path).data.publicUrl : null); return <article key={resource.id} className="rounded-lg border border-slate-200 bg-white p-4"><p className="text-xs font-semibold uppercase text-emerald-800">{resource.category} · {resource.content_type}</p><h3 className="mt-2 font-semibold text-slate-900">{resource.title}</h3>{resource.description && <p className="mt-2 text-sm text-slate-600">{resource.description}</p>}{href && <a href={href} target="_blank" rel="noreferrer" className="mt-4 inline-flex text-sm font-semibold text-emerald-800 hover:underline">{resource.cta_label || 'Open resource'}</a>}</article>; })}{resources.length === 0 && <p className="text-sm text-slate-500">No resources are available for your account.</p>}</div></section>}

        {tab === 'suggestions' && <section className="mt-6 max-w-2xl"><h2 className="text-xl font-bold text-slate-900">Contact the ACSES team</h2><form onSubmit={submitSuggestion} className="mt-4 space-y-4 rounded-lg border border-slate-200 bg-white p-5"><label className="block text-sm font-medium text-slate-700">Subject<input required maxLength={160} value={subject} onChange={(event) => setSubject(event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label><label className="block text-sm font-medium text-slate-700">Message<textarea required rows={5} value={message} onChange={(event) => setMessage(event.target.value)} className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" /></label><button disabled={busy} className="inline-flex items-center gap-2 rounded-md bg-emerald-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"><Send className="h-4 w-4" />{busy ? 'Sending...' : 'Send message'}</button></form></section>}

        {tab === 'elections' && <section className="mt-6"><h2 className="text-xl font-bold text-slate-900">Elections</h2><div className="mt-4 space-y-4">{elections.map((election) => { const voter = voterStatuses.find((item) => item.election_id === election.id); const electionCandidates = candidates.filter((candidate) => candidate.election_id === election.id); const open = election.status === 'active' && Date.now() >= new Date(election.start_at).getTime() && Date.now() < new Date(election.end_at).getTime(); return <article key={election.id} className="rounded-lg border border-slate-200 bg-white p-5"><div className="flex flex-wrap items-start justify-between gap-3"><div><p className="text-xs font-semibold uppercase text-emerald-800">{election.status}</p><h3 className="mt-1 text-lg font-bold text-slate-900">{election.title}</h3>{election.description && <p className="mt-2 text-sm text-slate-600">{election.description}</p>}</div><p className="text-xs text-slate-500">Ends {new Date(election.end_at).toLocaleString()}</p></div>{!voter?.is_eligible ? <p className="mt-4 text-sm text-slate-500">You are not on the voter list for this election.</p> : voter.has_voted ? <p className="mt-4 text-sm font-semibold text-emerald-800">Ballot submitted</p> : !open ? <p className="mt-4 text-sm text-slate-500">Voting is not currently open.</p> : <div className="mt-4 flex flex-wrap gap-3"><select aria-label={`Candidate for ${election.title}`} value={selectedCandidates[election.id] ?? ''} onChange={(event) => setSelectedCandidates((current) => ({ ...current, [election.id]: event.target.value }))} className="h-10 min-w-56 rounded-md border border-slate-300 bg-white px-3"><option value="">Choose candidate</option>{electionCandidates.map((candidate) => <option key={candidate.id} value={candidate.id}>{positions.find((position) => position.id === candidate.position_id)?.title ?? 'Position'} · {candidate.name}{candidate.short_description ? ` · ${candidate.short_description}` : ''}</option>)}</select><button disabled={busy || !selectedCandidates[election.id]} onClick={() => void castVote(election.id)} className="rounded-md bg-emerald-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Submit ballot</button></div>}</article>; })}{elections.length === 0 && <p className="text-sm text-slate-500">There are no elections available for your account.</p>}</div></section>}
        {tab === 'timeline' && <StudentTimeline />}
        {tab === 'results' && <StudentElectionResults />}
      </div>
    </main>
  );
};

interface TimelineEntry {
  id: string;
  title: string;
  summary: string | null;
  description: string | null;
  starts_at: string;
  academic_year: string | null;
  cta_label: string | null;
  cta_url: string | null;
}

const StudentTimeline: React.FC = () => {
  const { client } = usePortal();
  const [entries, setEntries] = useState<TimelineEntry[]>([]);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!client) return;
    let active = true;
    void client.from('portal_academic_timeline_entries')
      .select('id, title, summary, description, starts_at, academic_year, cta_label, cta_url')
      .order('starts_at')
      .then(({ data, error: queryError }) => {
        if (!active) return;
        if (queryError) setError(queryError.message);
        else setEntries((data ?? []) as TimelineEntry[]);
      });
    return () => { active = false; };
  }, [client]);

  return <section className="mt-6"><h2 className="text-xl font-bold text-slate-900">Academic timeline</h2>{error && <p role="alert" className="mt-3 text-sm text-rose-800">{error}</p>}<div className="mt-4 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">{entries.map((entry) => <article key={entry.id} className="p-4"><p className="text-xs font-semibold uppercase text-emerald-800">{entry.academic_year || 'Academic calendar'} · {new Date(entry.starts_at).toLocaleDateString()}</p><h3 className="mt-1 font-semibold text-slate-900">{entry.title}</h3><p className="mt-2 text-sm text-slate-600">{entry.summary || entry.description}</p>{entry.cta_url && <a href={entry.cta_url} className="mt-3 inline-flex text-sm font-semibold text-emerald-800">{entry.cta_label || 'More information'}</a>}</article>)}{entries.length === 0 && !error && <p className="p-4 text-sm text-slate-500">No published timeline entries.</p>}</div></section>;
};

interface ElectionResult {
  position_id: string;
  position_title: string;
  candidate_id: string;
  candidate_name: string;
  vote_count: number;
}

const StudentElectionResults: React.FC = () => {
  const { client } = usePortal();
  const [results, setResults] = useState<Record<string, ElectionResult[]>>({});
  const [titles, setTitles] = useState<Record<string, string>>({});
  const [error, setError] = useState('');

  useEffect(() => {
    if (!client) return;
    let active = true;
    void client.from('portal_elections')
      .select('id, title')
      .lte('end_at', new Date().toISOString())
      .then(async ({ data, error: queryError }) => {
        if (!active) return;
        if (queryError) {
          setError(queryError.message);
          return;
        }
        const endedElections = data ?? [];
        const nextTitles = Object.fromEntries(endedElections.map((election) => [election.id, election.title]));
        const resultSets = await Promise.all(endedElections.map(async (election) => {
          const { data: rows, error: resultError } = await client.rpc('get_election_results', { p_election_id: election.id });
          if (resultError) throw resultError;
          return [election.id, (rows ?? []) as ElectionResult[]] as const;
        }));
        if (!active) return;
        setTitles(nextTitles);
        setResults(Object.fromEntries(resultSets));
      })
      .catch((resultError: Error) => {
        if (active) setError(resultError.message);
      });
    return () => { active = false; };
  }, [client]);

  return <section className="mt-6"><h2 className="text-xl font-bold text-slate-900">Election results</h2>{error && <p role="alert" className="mt-3 text-sm text-rose-800">{error}</p>}<div className="mt-4 space-y-4">{Object.entries(results).map(([electionId, rows]) => <article key={electionId} className="rounded-lg border border-slate-200 bg-white p-5"><h3 className="font-bold text-slate-900">{titles[electionId]}</h3><div className="mt-3 divide-y divide-slate-200">{rows.map((row) => <div key={row.candidate_id} className="flex justify-between gap-4 py-2 text-sm"><span>{row.position_title} · {row.candidate_name}</span><strong>{row.vote_count}</strong></div>)}</div></article>)}{Object.keys(results).length === 0 && !error && <p className="text-sm text-slate-500">No election results are published yet.</p>}</div></section>;
};
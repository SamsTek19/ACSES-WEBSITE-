import React, { useEffect, useState } from 'react';
import { Link, Navigate } from 'react-router-dom';
import { ArrowLeft, Plus, Vote } from 'lucide-react';
import { usePortal } from './PortalApp';

interface ElectionRow {
  id: string;
  title: string;
  description: string | null;
  start_at: string;
  end_at: string;
  status: 'draft' | 'active' | 'completed' | 'cancelled';
}

interface PositionRow { id: string; title: string; display_order: number }
interface CandidateRow { id: string; name: string; position_id: string; short_description: string | null }
interface VoterRow { profile_id: string; has_voted: boolean; is_eligible: boolean }
interface StudentOption { id: string; full_name: string; email: string; index_number: string | null }
interface ResultRow { candidate_id: string; position_title: string; candidate_name: string; vote_count: number }

export const AdminElectionsPage: React.FC = () => {
  const { client, session, profile, loading, configurationMissing } = usePortal();
  const [elections, setElections] = useState<ElectionRow[]>([]);
  const [students, setStudents] = useState<StudentOption[]>([]);
  const [selectedElection, setSelectedElection] = useState('');
  const [positions, setPositions] = useState<PositionRow[]>([]);
  const [candidates, setCandidates] = useState<CandidateRow[]>([]);
  const [voters, setVoters] = useState<VoterRow[]>([]);
  const [results, setResults] = useState<ResultRow[]>([]);
  const [selectedStudents, setSelectedStudents] = useState<string[]>([]);
  const [form, setForm] = useState({ title: '', description: '', startAt: '', endAt: '' });
  const [positionTitle, setPositionTitle] = useState('');
  const [candidateForm, setCandidateForm] = useState({ name: '', positionId: '', shortDescription: '' });
  const [error, setError] = useState('');
  const [notice, setNotice] = useState('');
  const [busy, setBusy] = useState(false);

  const reload = async (): Promise<void> => {
    if (!client) return;
    const [electionResult, studentResult] = await Promise.all([
      client.from('portal_elections').select('id, title, description, start_at, end_at, status').order('start_at', { ascending: false }),
      client.from('portal_profiles').select('id, full_name, email, index_number').eq('role', 'student').eq('account_status', 'approved').order('full_name'),
    ]);
    if (electionResult.error || studentResult.error) {
      setError(electionResult.error?.message ?? studentResult.error?.message ?? 'Could not load elections.');
      return;
    }
    const nextElections = (electionResult.data ?? []) as ElectionRow[];
    setElections(nextElections);
    setStudents((studentResult.data ?? []) as StudentOption[]);
    if (!selectedElection && nextElections[0]) setSelectedElection(nextElections[0].id);
  };

  const reloadSelected = async (electionId: string): Promise<void> => {
    if (!client || !electionId) return;
    const [positionResult, candidateResult, voterResult] = await Promise.all([
      client.from('portal_election_positions').select('id, title, display_order').eq('election_id', electionId).order('display_order'),
      client.from('portal_election_candidates').select('id, name, position_id, short_description').eq('election_id', electionId),
      client.from('portal_election_voters').select('profile_id, has_voted, is_eligible').eq('election_id', electionId),
    ]);
    const queryError = positionResult.error || candidateResult.error || voterResult.error;
    if (queryError) {
      setError(queryError.message);
      return;
    }
    setPositions((positionResult.data ?? []) as PositionRow[]);
    setCandidates((candidateResult.data ?? []) as CandidateRow[]);
    setVoters((voterResult.data ?? []) as VoterRow[]);
  };

  useEffect(() => {
    if (profile?.role === 'admin') void reload();
  }, [client, profile?.role]);

  useEffect(() => {
    if (profile?.role === 'admin' && selectedElection) {
      setResults([]);
      void reloadSelected(selectedElection);
    }
  }, [client, profile?.role, selectedElection]);

  if (configurationMissing) return <main className="p-8 text-sm text-amber-900">Configure Supabase to manage elections.</main>;
  if (loading) return <main className="p-8 text-sm text-slate-600">Loading account...</main>;
  if (!session) return <Navigate to="/portal/login" replace />;
  if (profile?.role !== 'admin' || profile.account_status !== 'approved') return <main className="p-8 text-sm text-rose-800">Administrator access is required.</main>;
  if (!client) return null;

  const createElection = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    setBusy(true);
    setError('');
    const { data, error: insertError } = await client.from('portal_elections').insert({
      title: form.title.trim(),
      description: form.description.trim() || null,
      start_at: new Date(form.startAt).toISOString(),
      end_at: new Date(form.endAt).toISOString(),
      status: 'draft',
      created_by: profile.id,
    }).select('id').single();
    setBusy(false);
    if (insertError) { setError(insertError.message); return; }
    setForm({ title: '', description: '', startAt: '', endAt: '' });
    setSelectedElection(data.id);
    setNotice('Election created. Add positions, candidates, and eligible voters before activation.');
    await reload();
  };

  const createPosition = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    setBusy(true);
    const { error: insertError } = await client.from('portal_election_positions').insert({ election_id: selectedElection, title: positionTitle.trim(), display_order: positions.length });
    setBusy(false);
    if (insertError) setError(insertError.message);
    else { setPositionTitle(''); setNotice('Position added.'); await reloadSelected(selectedElection); }
  };

  const createCandidate = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    setBusy(true);
    const { error: insertError } = await client.from('portal_election_candidates').insert({
      election_id: selectedElection,
      position_id: candidateForm.positionId,
      name: candidateForm.name.trim(),
      short_description: candidateForm.shortDescription.trim() || null,
    });
    setBusy(false);
    if (insertError) setError(insertError.message);
    else { setCandidateForm({ name: '', positionId: candidateForm.positionId, shortDescription: '' }); setNotice('Candidate added.'); await reloadSelected(selectedElection); }
  };

  const assignVoters = async (): Promise<void> => {
    if (!selectedStudents.length) return;
    setBusy(true);
    const { error: insertError } = await client.from('portal_election_voters').upsert(
      selectedStudents.map((profileId) => ({ election_id: selectedElection, profile_id: profileId, is_eligible: true })),
      { onConflict: 'election_id,profile_id' },
    );
    setBusy(false);
    if (insertError) setError(insertError.message);
    else { setSelectedStudents([]); setNotice('Eligible voter list updated.'); await reloadSelected(selectedElection); }
  };

  const updateStatus = async (status: ElectionRow['status']): Promise<void> => {
    const { error: updateError } = await client.from('portal_elections').update({ status }).eq('id', selectedElection);
    if (updateError) setError(updateError.message);
    else { setNotice(`Election status set to ${status}.`); await reload(); }
  };

  const loadResults = async (): Promise<void> => {
    const { data, error: resultsError } = await client.rpc('get_election_results', { p_election_id: selectedElection });
    if (resultsError) setError(resultsError.message);
    else setResults((data ?? []) as ResultRow[]);
  };

  const currentElection = elections.find((election) => election.id === selectedElection);

  return (
    <main className="min-h-screen bg-slate-50">
      <header className="border-b border-slate-200 bg-white"><div className="mx-auto max-w-7xl px-4 py-4 sm:px-6"><Link to="/portal" className="inline-flex items-center gap-2 text-sm font-semibold text-emerald-900"><ArrowLeft className="h-4 w-4" />Portal dashboard</Link></div></header>
      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6"><p className="text-xs font-bold uppercase text-emerald-800">Admin</p><h1 className="mt-1 text-2xl font-bold text-slate-900">Election management</h1>
        {error && <p role="alert" className="mt-4 rounded-md bg-rose-50 p-3 text-sm text-rose-800">{error}</p>}{notice && <p role="status" className="mt-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-900">{notice}</p>}
        <div className="mt-6 grid gap-6 lg:grid-cols-[minmax(260px,0.75fr)_minmax(0,1.25fr)]">
          <section className="space-y-5">
            <form onSubmit={createElection} className="space-y-3 rounded-lg border border-slate-200 bg-white p-5"><h2 className="font-bold text-slate-900">Create election</h2><label className="block text-sm">Title<input required value={form.title} onChange={(event) => setForm((current) => ({ ...current, title: event.target.value }))} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label><label className="block text-sm">Description<textarea value={form.description} onChange={(event) => setForm((current) => ({ ...current, description: event.target.value }))} rows={2} className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" /></label><label className="block text-sm">Starts<input required type="datetime-local" value={form.startAt} onChange={(event) => setForm((current) => ({ ...current, startAt: event.target.value }))} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label><label className="block text-sm">Ends<input required type="datetime-local" value={form.endAt} onChange={(event) => setForm((current) => ({ ...current, endAt: event.target.value }))} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label><button disabled={busy} className="rounded-md bg-emerald-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">Create draft</button></form>
            <section className="rounded-lg border border-slate-200 bg-white p-5"><h2 className="font-bold text-slate-900">Existing elections</h2><div className="mt-3 space-y-2">{elections.map((election) => <button key={election.id} onClick={() => setSelectedElection(election.id)} className={`block w-full rounded-md border p-3 text-left ${selectedElection === election.id ? 'border-emerald-800 bg-emerald-50' : 'border-slate-200'}`}><span className="block font-semibold text-slate-900">{election.title}</span><span className="mt-1 block text-xs uppercase text-slate-500">{election.status}</span></button>)}{elections.length === 0 && <p className="text-sm text-slate-500">No elections created.</p>}</div></section>
          </section>
          <section className="space-y-5">
            {!currentElection ? <div className="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500">Create or select an election to manage its ballot.</div> : <>
              <div className="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white p-5"><div><p className="text-xs uppercase text-slate-500">Selected election</p><h2 className="mt-1 text-lg font-bold text-slate-900">{currentElection.title}</h2><p className="mt-1 text-sm text-slate-600">{new Date(currentElection.start_at).toLocaleString()} – {new Date(currentElection.end_at).toLocaleString()}</p></div><div className="flex flex-wrap gap-2">{currentElection.status === 'draft' && <button onClick={() => void updateStatus('active')} className="rounded-md bg-emerald-900 px-3 py-2 text-sm font-semibold text-white">Activate</button>}{currentElection.status === 'active' && <button onClick={() => void updateStatus('completed')} className="rounded-md bg-slate-800 px-3 py-2 text-sm font-semibold text-white">Complete</button>}<button onClick={() => void loadResults()} className="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold">Results</button></div></div>
              <div className="grid gap-5 md:grid-cols-2">
                <form onSubmit={createPosition} className="space-y-3 rounded-lg border border-slate-200 bg-white p-5"><h3 className="font-bold text-slate-900">Ballot positions</h3><label className="block text-sm">Position title<input required value={positionTitle} onChange={(event) => setPositionTitle(event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label><button disabled={busy} className="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold">Add position</button><ul className="space-y-1 text-sm text-slate-600">{positions.map((position) => <li key={position.id}>{position.title}</li>)}</ul></form>
                <form onSubmit={createCandidate} className="space-y-3 rounded-lg border border-slate-200 bg-white p-5"><h3 className="font-bold text-slate-900">Candidates</h3><label className="block text-sm">Position<select required value={candidateForm.positionId} onChange={(event) => setCandidateForm((current) => ({ ...current, positionId: event.target.value }))} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3"><option value="">Select position</option>{positions.map((position) => <option key={position.id} value={position.id}>{position.title}</option>)}</select></label><label className="block text-sm">Candidate name<input required value={candidateForm.name} onChange={(event) => setCandidateForm((current) => ({ ...current, name: event.target.value }))} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label><label className="block text-sm">Short description<input value={candidateForm.shortDescription} onChange={(event) => setCandidateForm((current) => ({ ...current, shortDescription: event.target.value }))} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label><button disabled={busy || !positions.length} className="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold">Add candidate</button><ul className="space-y-1 text-sm text-slate-600">{candidates.map((candidate) => <li key={candidate.id}>{candidate.name}</li>)}</ul></form>
              </div>
              <section className="rounded-lg border border-slate-200 bg-white p-5"><h3 className="font-bold text-slate-900">Eligible voters</h3><p className="mt-1 text-sm text-slate-600">Select approved students who may sign in and cast one ballot.</p><div className="mt-3 max-h-56 space-y-2 overflow-y-auto">{students.map((student) => { const voter = voters.find((item) => item.profile_id === student.id); const assigned = voter?.is_eligible ?? false; return <label key={student.id} className="flex items-center gap-3 text-sm"><input type="checkbox" disabled={assigned} checked={assigned || selectedStudents.includes(student.id)} onChange={(event) => setSelectedStudents((current) => event.target.checked ? [...current, student.id] : current.filter((id) => id !== student.id))} /><span>{student.full_name} <span className="text-slate-500">{student.index_number || student.email}</span></span>{assigned && <span className="ml-auto text-xs text-emerald-800">Eligible{voter?.has_voted ? ' · Voted' : ''}</span>}</label>; })}</div><button onClick={() => void assignVoters()} disabled={busy || !selectedStudents.length} className="mt-4 inline-flex items-center gap-2 rounded-md bg-emerald-900 px-3 py-2 text-sm font-semibold text-white disabled:opacity-50"><Plus className="h-4 w-4" />Add selected voters ({selectedStudents.length})</button></section>
              {results.length > 0 && <section className="rounded-lg border border-slate-200 bg-white p-5"><h3 className="inline-flex items-center gap-2 font-bold text-slate-900"><Vote className="h-4 w-4" />Election results</h3><div className="mt-3 divide-y divide-slate-200">{results.map((result) => <div key={result.candidate_id} className="flex justify-between gap-4 py-2 text-sm"><span>{result.position_title} · {result.candidate_name}</span><strong>{result.vote_count}</strong></div>)}</div></section>}
            </>}
          </section>
        </div>
      </div>
    </main>
  );
};

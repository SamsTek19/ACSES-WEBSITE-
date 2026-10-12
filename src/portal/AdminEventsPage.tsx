import React, { useEffect, useState } from 'react';
import { Link, Navigate } from 'react-router-dom';
import { ArrowLeft, CalendarPlus, Pencil, Trash2 } from 'lucide-react';
import { usePortal } from './PortalApp';

interface PortalEvent {
  id: string;
  title: string;
  description: string | null;
  location: string | null;
  start_at: string | null;
  end_at: string | null;
  time_label: string | null;
  category: string | null;
  cta_url: string | null;
  memories_link: string | null;
  banner_path: string | null;
  banner_alt: string | null;
}

interface EventForm {
  title: string;
  description: string;
  location: string;
  startAt: string;
  endAt: string;
  timeLabel: string;
  category: string;
  ctaUrl: string;
  memoriesLink: string;
  bannerPath: string;
  bannerAlt: string;
}

const emptyForm: EventForm = {
  title: '', description: '', location: '', startAt: '', endAt: '', timeLabel: '',
  category: '', ctaUrl: '', memoriesLink: '', bannerPath: '', bannerAlt: '',
};

const toLocalDateTime = (value: string | null): string => value
  ? new Date(value).toISOString().slice(0, 16)
  : '';

const fromEvent = (event: PortalEvent): EventForm => ({
  title: event.title,
  description: event.description ?? '',
  location: event.location ?? '',
  startAt: toLocalDateTime(event.start_at),
  endAt: toLocalDateTime(event.end_at),
  timeLabel: event.time_label ?? '',
  category: event.category ?? '',
  ctaUrl: event.cta_url ?? '',
  memoriesLink: event.memories_link ?? '',
  bannerPath: event.banner_path ?? '',
  bannerAlt: event.banner_alt ?? '',
});

export const AdminEventsPage: React.FC = () => {
  const { client, profile, session, loading, configurationMissing } = usePortal();
  const [events, setEvents] = useState<PortalEvent[]>([]);
  const [form, setForm] = useState<EventForm>(emptyForm);
  const [editingId, setEditingId] = useState<string | null>(null);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');
  const [notice, setNotice] = useState('');

  useEffect(() => {
    if (!client || profile?.role !== 'admin') return;
    let active = true;

    void client.from('portal_events').select('*').order('start_at', { ascending: true, nullsFirst: false })
      .then(({ data, error: queryError }) => {
        if (!active) return;
        if (queryError) setError(queryError.message);
        else setEvents((data ?? []) as PortalEvent[]);
      });

    return () => { active = false; };
  }, [client, profile?.role]);

  if (configurationMissing) return <main className="p-8 text-sm text-amber-900">Configure the Supabase URL and publishable key to manage events.</main>;
  if (loading) return <main className="p-8 text-sm text-slate-600">Loading account...</main>;
  if (!session) return <Navigate to="/portal/login" replace />;
  if (profile?.role !== 'admin' || profile.account_status !== 'approved') return <main className="p-8 text-sm text-rose-800">Administrator access is required.</main>;
  if (!client) return null;

  const updateForm = (field: keyof EventForm, value: string): void => setForm((current) => ({ ...current, [field]: value }));
  const reload = async (): Promise<void> => {
    const { data, error: queryError } = await client.from('portal_events').select('*').order('start_at', { ascending: true, nullsFirst: false });
    if (queryError) setError(queryError.message);
    else setEvents((data ?? []) as PortalEvent[]);
  };

  const save = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    setBusy(true);
    setError('');
    setNotice('');
    const values = {
      title: form.title.trim(),
      description: form.description.trim() || null,
      location: form.location.trim() || null,
      start_at: form.startAt ? new Date(form.startAt).toISOString() : null,
      end_at: form.endAt ? new Date(form.endAt).toISOString() : null,
      time_label: form.timeLabel.trim() || null,
      category: form.category.trim() || null,
      cta_url: form.ctaUrl.trim() || null,
      memories_link: form.memoriesLink.trim() || null,
      banner_path: form.bannerPath.trim() || null,
      banner_alt: form.bannerAlt.trim() || null,
      created_by: profile.id,
    };
    const result = editingId
      ? await client.from('portal_events').update(values).eq('id', editingId)
      : await client.from('portal_events').insert(values);
    setBusy(false);
    if (result.error) {
      setError(result.error.message);
      return;
    }

    setNotice(editingId ? 'Event updated.' : 'Event created.');
    setForm(emptyForm);
    setEditingId(null);
    await reload();
  };

  const remove = async (eventId: string): Promise<void> => {
    if (!window.confirm('Delete this event?')) return;
    const { error: deleteError } = await client.from('portal_events').delete().eq('id', eventId);
    if (deleteError) setError(deleteError.message);
    else {
      setNotice('Event deleted.');
      await reload();
    }
  };

  return (
    <main className="min-h-screen bg-slate-50">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6">
          <Link to="/portal" className="inline-flex items-center gap-2 text-sm font-semibold text-emerald-900"><ArrowLeft className="h-4 w-4" />Portal dashboard</Link>
          <p className="text-sm font-semibold text-slate-700">Event management</p>
        </div>
      </header>
      <div className="mx-auto grid max-w-7xl gap-8 px-4 py-8 sm:px-6 lg:grid-cols-[minmax(0,1fr)_minmax(320px,0.8fr)]">
        <section>
          <div className="mb-5 flex items-end justify-between gap-4"><div><p className="text-xs font-bold uppercase text-emerald-800">Admin</p><h1 className="mt-1 text-2xl font-bold text-slate-900">Events</h1></div><button onClick={() => { setForm(emptyForm); setEditingId(null); }} className="inline-flex items-center gap-2 rounded-md bg-emerald-900 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-800"><CalendarPlus className="h-4 w-4" />New event</button></div>
          {error && <p role="alert" className="mb-4 rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{error}</p>}
          {notice && <p role="status" className="mb-4 rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900">{notice}</p>}
          <div className="divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">
            {events.length === 0 && <p className="p-5 text-sm text-slate-500">No events have been added.</p>}
            {events.map((item) => <article key={item.id} className="flex items-start justify-between gap-4 p-4"><div className="min-w-0"><h2 className="font-semibold text-slate-900">{item.title}</h2><p className="mt-1 text-sm text-slate-500">{item.start_at ? new Date(item.start_at).toLocaleString() : 'Unscheduled'}{item.location ? ` · ${item.location}` : ''}</p></div><div className="flex shrink-0 gap-1"><button aria-label={`Edit ${item.title}`} onClick={() => { setEditingId(item.id); setForm(fromEvent(item)); }} className="rounded p-2 text-slate-600 hover:bg-slate-100"><Pencil className="h-4 w-4" /></button><button aria-label={`Delete ${item.title}`} onClick={() => void remove(item.id)} className="rounded p-2 text-rose-700 hover:bg-rose-50"><Trash2 className="h-4 w-4" /></button></div></article>)}
          </div>
        </section>
        <section className="h-fit rounded-lg border border-slate-200 bg-white p-5">
          <h2 className="text-lg font-bold text-slate-900">{editingId ? 'Edit event' : 'Create event'}</h2>
          <form onSubmit={save} className="mt-4 space-y-3">
            <label className="block text-sm font-medium text-slate-700">Title<input required maxLength={255} value={form.title} onChange={(event) => updateForm('title', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <label className="block text-sm font-medium text-slate-700">Description<textarea rows={3} value={form.description} onChange={(event) => updateForm('description', event.target.value)} className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" /></label>
            <div className="grid gap-3 sm:grid-cols-2">
              <label className="text-sm font-medium text-slate-700">Start<input type="datetime-local" value={form.startAt} onChange={(event) => updateForm('startAt', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-2" /></label>
              <label className="text-sm font-medium text-slate-700">End<input type="datetime-local" value={form.endAt} onChange={(event) => updateForm('endAt', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-2" /></label>
            </div>
            <div className="grid gap-3 sm:grid-cols-2">
              <label className="text-sm font-medium text-slate-700">Location<input value={form.location} onChange={(event) => updateForm('location', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
              <label className="text-sm font-medium text-slate-700">Display time if unscheduled<input value={form.timeLabel} onChange={(event) => updateForm('timeLabel', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            </div>
            <label className="block text-sm font-medium text-slate-700">Category<input value={form.category} onChange={(event) => updateForm('category', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <label className="block text-sm font-medium text-slate-700">Details URL<input type="url" value={form.ctaUrl} onChange={(event) => updateForm('ctaUrl', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <label className="block text-sm font-medium text-slate-700">Memories URL<input type="url" value={form.memoriesLink} onChange={(event) => updateForm('memoriesLink', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <label className="block text-sm font-medium text-slate-700">Banner URL<input type="url" value={form.bannerPath} onChange={(event) => updateForm('bannerPath', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <label className="block text-sm font-medium text-slate-700">Banner alt text<input value={form.bannerAlt} onChange={(event) => updateForm('bannerAlt', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <div className="flex gap-2 pt-2"><button type="submit" disabled={busy} className="rounded-md bg-emerald-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">{busy ? 'Saving...' : editingId ? 'Save event' : 'Create event'}</button>{editingId && <button type="button" onClick={() => { setEditingId(null); setForm(emptyForm); }} className="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold">Cancel</button>}</div>
          </form>
        </section>
      </div>
    </main>
  );
};
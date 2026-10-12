import React, { useEffect, useState } from 'react';
import { Link, Navigate } from 'react-router-dom';
import { ArrowLeft, Plus } from 'lucide-react';
import { usePortal } from './PortalApp';

type ContentKind = 'portal_announcements' | 'portal_support_resources' | 'portal_academic_timeline_entries';
type ContentRow = { id: string; title: string; subtitle: string; status: string };

const contentTabs: { id: ContentKind; label: string }[] = [
  { id: 'portal_announcements', label: 'Announcements' },
  { id: 'portal_support_resources', label: 'Resources' },
  { id: 'portal_academic_timeline_entries', label: 'Academic timeline' },
];

const defaultFields: Record<ContentKind, Record<string, string>> = {
  portal_announcements: { title: '', slug: '', excerpt: '', content: '', type: 'general', priority: 'normal', publishedAt: '' },
  portal_support_resources: { title: '', category: 'support', resourceType: 'link', contentType: 'general', description: '', ctaLabel: 'Open resource', ctaUrl: '', visibility: 'student' },
  portal_academic_timeline_entries: { title: '', summary: '', startsAt: '', academicYear: '', ctaLabel: '', ctaUrl: '' },
};

const fields: Record<ContentKind, { key: string; label: string; multiline?: boolean; type?: string }[]> = {
  portal_announcements: [
    { key: 'title', label: 'Title' }, { key: 'slug', label: 'URL slug' }, { key: 'excerpt', label: 'Excerpt' },
    { key: 'content', label: 'Content', multiline: true }, { key: 'priority', label: 'Priority' }, { key: 'publishedAt', label: 'Publish at', type: 'datetime-local' },
  ],
  portal_support_resources: [
    { key: 'title', label: 'Title' }, { key: 'category', label: 'Category' }, { key: 'contentType', label: 'Content type' },
    { key: 'description', label: 'Description', multiline: true }, { key: 'ctaLabel', label: 'Link label' }, { key: 'ctaUrl', label: 'Resource URL' },
  ],
  portal_academic_timeline_entries: [
    { key: 'title', label: 'Title' }, { key: 'summary', label: 'Summary' }, { key: 'startsAt', label: 'Date and time', type: 'datetime-local' },
    { key: 'academicYear', label: 'Academic year' }, { key: 'ctaLabel', label: 'Link label' }, { key: 'ctaUrl', label: 'Link URL' },
  ],
};

export const AdminContentPage: React.FC = () => {
  const { client, session, profile, loading, configurationMissing } = usePortal();
  const [kind, setKind] = useState<ContentKind>('portal_announcements');
  const [rows, setRows] = useState<ContentRow[]>([]);
  const [form, setForm] = useState<Record<string, string>>(defaultFields.portal_announcements);
  const [resourceFile, setResourceFile] = useState<File | null>(null);
  const [error, setError] = useState('');
  const [notice, setNotice] = useState('');
  const [busy, setBusy] = useState(false);

  const reload = async (selectedKind: ContentKind): Promise<void> => {
    if (!client) return;
    const { data, error: queryError } = await client.from(selectedKind).select('*').order('created_at', { ascending: false });
    if (queryError) {
      setError(queryError.message);
      return;
    }
    setRows((data ?? []).map((row: Record<string, unknown>) => ({
      id: String(row.id),
      title: String(row.title),
      subtitle: selectedKind === 'portal_announcements'
        ? `${String(row.priority)} · ${row.published_at ? 'Published' : 'Draft'}`
        : selectedKind === 'portal_academic_timeline_entries'
          ? String(row.starts_at ?? '')
          : String(row.category ?? ''),
      status: selectedKind === 'portal_academic_timeline_entries' && row.is_published === false ? 'Draft' : 'Published',
    })));
  };

  useEffect(() => {
    if (profile?.role === 'admin') void reload(kind);
  }, [client, kind, profile?.role]);

  if (configurationMissing) return <main className="p-8 text-sm text-amber-900">Configure Supabase to manage portal content.</main>;
  if (loading) return <main className="p-8 text-sm text-slate-600">Loading account...</main>;
  if (!session) return <Navigate to="/portal/login" replace />;
  if (profile?.role !== 'admin' || profile.account_status !== 'approved') return <main className="p-8 text-sm text-rose-800">Administrator access is required.</main>;
  if (!client) return null;

  const selectKind = (nextKind: ContentKind): void => {
    setKind(nextKind);
    setForm(defaultFields[nextKind]);
    setResourceFile(null);
    setError('');
    setNotice('');
  };

  const save = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    setBusy(true);
    setError('');
    setNotice('');
    let payload: Record<string, unknown>;
    let resourceFilePath: string | null = null;
    if (kind === 'support_resources' && resourceFile) {
      const safeFileName = resourceFile.name.replace(/[^a-zA-Z0-9._-]/g, '-');
      resourceFilePath = `resources/${crypto.randomUUID()}-${safeFileName}`;
      const { error: uploadError } = await client.storage.from('student-private').upload(resourceFilePath, resourceFile, {
        contentType: resourceFile.type || 'application/octet-stream',
        upsert: false,
      });
      if (uploadError) {
        setBusy(false);
        setError(uploadError.message);
        return;
      }
    }
    if (kind === 'portal_announcements') {
      payload = {
        title: form.title.trim(),
        slug: form.slug.trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, ''),
        excerpt: form.excerpt.trim() || null,
        content: form.content.trim() || null,
        priority: form.priority,
        published_at: form.publishedAt ? new Date(form.publishedAt).toISOString() : null,
        author_id: profile.id,
      };
    } else if (kind === 'portal_support_resources') {
      payload = {
        title: form.title.trim(), category: form.category.trim(), content_type: form.contentType,
        description: form.description.trim() || null, cta_label: form.ctaLabel.trim() || null,
        resource_type: resourceFile ? 'file' : form.resourceType,
        cta_url: form.ctaUrl.trim() || null, file_path: resourceFilePath, visibility: form.visibility,
        target_classes: [], target_years: [], created_by: profile.id,
      };
    } else {
      payload = {
        title: form.title.trim(), summary: form.summary.trim() || null,
        starts_at: form.startsAt ? new Date(form.startsAt).toISOString() : null,
        academic_year: form.academicYear.trim() || null, cta_label: form.ctaLabel.trim() || null,
        cta_url: form.ctaUrl.trim() || null, is_published: true, created_by: profile.id,
      };
    }

    const { error: insertError } = await client.from(kind).insert(payload);
    setBusy(false);
    if (insertError) {
      setError(insertError.message);
      return;
    }
    setNotice('Content published.');
    setForm(defaultFields[kind]);
    setResourceFile(null);
    await reload(kind);
  };

  return (
    <main className="min-h-screen bg-slate-50">
      <header className="border-b border-slate-200 bg-white"><div className="mx-auto max-w-7xl px-4 py-4 sm:px-6"><Link to="/portal" className="inline-flex items-center gap-2 text-sm font-semibold text-emerald-900"><ArrowLeft className="h-4 w-4" />Portal dashboard</Link></div></header>
      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6"><p className="text-xs font-bold uppercase text-emerald-800">Admin</p><h1 className="mt-1 text-2xl font-bold text-slate-900">Content management</h1>
        <div role="tablist" aria-label="Content type" className="mt-5 flex flex-wrap gap-2 border-b border-slate-200 pb-3">{contentTabs.map((tab) => <button key={tab.id} role="tab" aria-selected={kind === tab.id} onClick={() => selectKind(tab.id)} className={`rounded-md px-3 py-2 text-sm font-semibold ${kind === tab.id ? 'bg-emerald-900 text-white' : 'text-slate-600 hover:bg-white'}`}>{tab.label}</button>)}</div>
        {error && <p role="alert" className="mt-4 rounded-md bg-rose-50 p-3 text-sm text-rose-800">{error}</p>}{notice && <p role="status" className="mt-4 rounded-md bg-emerald-50 p-3 text-sm text-emerald-900">{notice}</p>}
        <div className="mt-6 grid gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(320px,0.8fr)]">
          <section className="divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">{rows.map((row) => <article key={row.id} className="p-4"><h2 className="font-semibold text-slate-900">{row.title}</h2><p className="mt-1 text-sm text-slate-500">{row.subtitle || row.status}</p></article>)}{rows.length === 0 && <p className="p-4 text-sm text-slate-500">No items in this section yet.</p>}</section>
          <form onSubmit={save} className="h-fit space-y-3 rounded-lg border border-slate-200 bg-white p-5"><h2 className="text-lg font-bold text-slate-900">Create {contentTabs.find((tab) => tab.id === kind)?.label.toLowerCase()}</h2>{fields[kind].map((field) => <label key={field.key} className="block text-sm font-medium text-slate-700">{field.label}{field.multiline ? <textarea required={field.key === 'content'} rows={3} value={form[field.key] ?? ''} onChange={(event) => setForm((current) => ({ ...current, [field.key]: event.target.value }))} className="mt-1 w-full rounded-md border border-slate-300 px-3 py-2" /> : <input required={field.key === 'title' || field.key === 'slug' || field.key === 'startsAt'} type={field.type ?? 'text'} value={form[field.key] ?? ''} onChange={(event) => setForm((current) => ({ ...current, [field.key]: event.target.value }))} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" />}</label>)}
            {kind === 'portal_announcements' && <label className="block text-sm font-medium text-slate-700">Type<select value={form.type} onChange={(event) => setForm((current) => ({ ...current, type: event.target.value }))} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3"><option value="general">General</option><option value="security">Security</option><option value="maintenance">Maintenance</option></select></label>}
            {kind === 'portal_support_resources' && <>
              <label className="block text-sm font-medium text-slate-700">Visibility<select value={form.visibility} onChange={(event) => setForm((current) => ({ ...current, visibility: event.target.value }))} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3"><option value="student">Approved students</option><option value="public">Public</option></select></label>
              <label className="block text-sm font-medium text-slate-700">Resource file<input type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.zip" onChange={(event) => setResourceFile(event.target.files?.[0] ?? null)} className="mt-1 block w-full text-sm" /></label>
            </>}
            <button disabled={busy} className="inline-flex items-center gap-2 rounded-md bg-emerald-900 px-4 py-2 text-sm font-semibold text-white disabled:opacity-60"><Plus className="h-4 w-4" />{busy ? 'Saving...' : 'Create item'}</button>
          </form>
        </div>
      </div>
    </main>
  );
};
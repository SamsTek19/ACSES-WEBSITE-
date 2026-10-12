import React, { useEffect, useState } from 'react';
import { Link, Navigate } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';
import { usePortal } from './PortalApp';

interface SuggestionRow {
  id: string;
  sender_name: string | null;
  sender_email: string | null;
  category: string;
  subject: string;
  message: string;
  status: string;
  created_at: string;
}

export const AdminSuggestionsPage: React.FC = () => {
  const { client, session, profile, loading, configurationMissing } = usePortal();
  const [suggestions, setSuggestions] = useState<SuggestionRow[]>([]);
  const [error, setError] = useState('');

  const reload = async (): Promise<void> => {
    if (!client) return;
    const { data, error: queryError } = await client.from('portal_suggestions').select('*').order('created_at', { ascending: false });
    if (queryError) setError(queryError.message);
    else setSuggestions((data ?? []) as SuggestionRow[]);
  };

  useEffect(() => {
    if (profile?.role === 'admin') void reload();
  }, [client, profile?.role]);

  if (configurationMissing) return <main className="p-8 text-sm text-amber-900">Configure Supabase to review messages.</main>;
  if (loading) return <main className="p-8 text-sm text-slate-600">Loading account...</main>;
  if (!session) return <Navigate to="/portal/login" replace />;
  if (profile?.role !== 'admin' || profile.account_status !== 'approved') return <main className="p-8 text-sm text-rose-800">Administrator access is required.</main>;

  const updateStatus = async (suggestion: SuggestionRow, status: string): Promise<void> => {
    if (!client || !profile) return;
    const { error: updateError } = await client.from('portal_suggestions').update({
      status,
      handled_at: status === 'pending' ? null : new Date().toISOString(),
      handled_by: status === 'pending' ? null : profile.id,
    }).eq('id', suggestion.id);
    if (updateError) setError(updateError.message);
    else await reload();
  };

  return (
    <main className="min-h-screen bg-slate-50">
      <header className="border-b border-slate-200 bg-white"><div className="mx-auto max-w-7xl px-4 py-4 sm:px-6"><Link to="/portal" className="inline-flex items-center gap-2 text-sm font-semibold text-emerald-900"><ArrowLeft className="h-4 w-4" />Portal dashboard</Link></div></header>
      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6"><p className="text-xs font-bold uppercase text-emerald-800">Admin</p><h1 className="mt-1 text-2xl font-bold text-slate-900">Student messages</h1>
        {error && <p role="alert" className="mt-4 rounded-md bg-rose-50 p-3 text-sm text-rose-800">{error}</p>}
        <div className="mt-5 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">{suggestions.map((suggestion) => <article key={suggestion.id} className="p-5"><div className="flex flex-wrap items-start justify-between gap-3"><div><p className="text-xs font-semibold uppercase text-emerald-800">{suggestion.category} · {suggestion.status}</p><h2 className="mt-1 font-semibold text-slate-900">{suggestion.subject}</h2><p className="mt-1 text-xs text-slate-500">{suggestion.sender_name || 'Student'}{suggestion.sender_email ? ` · ${suggestion.sender_email}` : ''} · {new Date(suggestion.created_at).toLocaleString()}</p></div><select aria-label={`Status for ${suggestion.subject}`} value={suggestion.status} onChange={(event) => void updateStatus(suggestion, event.target.value)} className="h-9 rounded-md border border-slate-300 bg-white px-2 text-sm"><option value="pending">Pending</option><option value="in_progress">In progress</option><option value="resolved">Resolved</option><option value="rejected">Rejected</option></select></div><p className="mt-4 whitespace-pre-wrap text-sm leading-6 text-slate-700">{suggestion.message}</p></article>)}{suggestions.length === 0 && <p className="p-5 text-sm text-slate-500">No student messages received.</p>}</div>
      </div>
    </main>
  );
};
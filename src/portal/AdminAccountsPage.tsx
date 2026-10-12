import React, { useEffect, useState } from 'react';
import { Link, Navigate } from 'react-router-dom';
import { ArrowLeft, Check, X } from 'lucide-react';
import { usePortal } from './PortalApp';

interface StudentAccount {
  id: string;
  email: string;
  username: string;
  full_name: string;
  index_number: string | null;
  class_name: string | null;
  year: number | null;
  account_status: string;
  created_at: string;
}

export const AdminAccountsPage: React.FC = () => {
  const { client, session, profile, loading, configurationMissing } = usePortal();
  const [accounts, setAccounts] = useState<StudentAccount[]>([]);
  const [error, setError] = useState('');

  const reload = async (): Promise<void> => {
    if (!client) return;
    const { data, error: queryError } = await client.from('portal_profiles')
      .select('id, email, username, full_name, index_number, class_name, year, account_status, created_at')
      .eq('role', 'student')
      .order('created_at', { ascending: false });
    if (queryError) setError(queryError.message);
    else setAccounts((data ?? []) as StudentAccount[]);
  };

  useEffect(() => {
    if (profile?.role === 'admin') void reload();
  }, [client, profile?.role]);

  if (configurationMissing) return <main className="p-8 text-sm text-amber-900">Configure Supabase to review student accounts.</main>;
  if (loading) return <main className="p-8 text-sm text-slate-600">Loading account...</main>;
  if (!session) return <Navigate to="/portal/login" replace />;
  if (profile?.role !== 'admin' || profile.account_status !== 'approved') return <main className="p-8 text-sm text-rose-800">Administrator access is required.</main>;

  const review = async (accountId: string, accountStatus: 'approved' | 'rejected'): Promise<void> => {
    if (!client || !profile) return;
    const { error: updateError } = await client.from('portal_profiles').update({
      account_status: accountStatus,
      reviewed_by: profile.id,
      reviewed_at: new Date().toISOString(),
    }).eq('id', accountId);
    if (updateError) setError(updateError.message);
    else await reload();
  };

  return (
    <main className="min-h-screen bg-slate-50">
      <header className="border-b border-slate-200 bg-white"><div className="mx-auto max-w-7xl px-4 py-4 sm:px-6"><Link to="/portal" className="inline-flex items-center gap-2 text-sm font-semibold text-emerald-900"><ArrowLeft className="h-4 w-4" />Portal dashboard</Link></div></header>
      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6"><p className="text-xs font-bold uppercase text-emerald-800">Admin</p><h1 className="mt-1 text-2xl font-bold text-slate-900">Student account review</h1><p className="mt-2 text-sm text-slate-600">Approve verified student accounts before granting portal access.</p>
        {error && <p role="alert" className="mt-4 rounded-md bg-rose-50 p-3 text-sm text-rose-800">{error}</p>}
        <div className="mt-5 divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white">{accounts.map((account) => <article key={account.id} className="flex flex-wrap items-center justify-between gap-4 p-4"><div><h2 className="font-semibold text-slate-900">{account.full_name}</h2><p className="mt-1 text-sm text-slate-600">{account.email} · {account.index_number || account.username} · {account.class_name || 'Programme not set'} · Year {account.year ?? '-'}</p><p className="mt-1 text-xs font-semibold uppercase text-slate-500">{account.account_status}</p></div>{account.account_status === 'pending' && <div className="flex gap-2"><button onClick={() => void review(account.id, 'approved')} className="inline-flex items-center gap-1 rounded-md bg-emerald-900 px-3 py-2 text-sm font-semibold text-white"><Check className="h-4 w-4" />Approve</button><button onClick={() => void review(account.id, 'rejected')} className="inline-flex items-center gap-1 rounded-md border border-rose-300 px-3 py-2 text-sm font-semibold text-rose-800"><X className="h-4 w-4" />Reject</button></div>}</article>)}{accounts.length === 0 && <p className="p-4 text-sm text-slate-500">No student accounts have registered.</p>}</div>
      </div>
    </main>
  );
};
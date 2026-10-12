import React, { Suspense, createContext, lazy, useContext, useEffect, useState } from 'react';
import { Link, Navigate, Route, Routes, useNavigate } from 'react-router-dom';
import type { Session, SupabaseClient } from '@supabase/supabase-js';
import { ArrowLeft, LoaderCircle, LogOut, ShieldCheck } from 'lucide-react';
import { getSupabaseClient, isSupabaseConfigured } from '../lib/supabase';

const AdminEventsPage = lazy(() => import('./AdminEventsPage').then((module) => ({ default: module.AdminEventsPage })));
const StudentWorkspace = lazy(() => import('./StudentWorkspace').then((module) => ({ default: module.StudentWorkspace })));
const AdminAccountsPage = lazy(() => import('./AdminAccountsPage').then((module) => ({ default: module.AdminAccountsPage })));
const AdminContentPage = lazy(() => import('./AdminContentPage').then((module) => ({ default: module.AdminContentPage })));
const AdminElectionsPage = lazy(() => import('./AdminElectionsPage').then((module) => ({ default: module.AdminElectionsPage })));
const AdminSuggestionsPage = lazy(() => import('./AdminSuggestionsPage').then((module) => ({ default: module.AdminSuggestionsPage })));

interface PortalProfile {
  id: string;
  email: string;
  username: string;
  full_name: string;
  role: 'student' | 'admin' | 'faculty';
  account_status: 'pending' | 'approved' | 'rejected' | 'suspended';
  rejection_reason: string | null;
}

interface PortalContextValue {
  client: SupabaseClient | null;
  session: Session | null;
  profile: PortalProfile | null;
  loading: boolean;
  configurationMissing: boolean;
  error: string;
  reloadProfile: () => Promise<void>;
}

const PortalContext = createContext<PortalContextValue | null>(null);

export const usePortal = (): PortalContextValue => {
  const value = useContext(PortalContext);
  if (!value) throw new Error('Portal context is unavailable.');
  return value;
};

export const PortalApp: React.FC = () => {
  const configurationMissing = !isSupabaseConfigured;
  const client = configurationMissing ? null : getSupabaseClient();
  const [session, setSession] = useState<Session | null>(null);
  const [profile, setProfile] = useState<PortalProfile | null>(null);
  const [loading, setLoading] = useState(!configurationMissing);
  const [error, setError] = useState('');

  const reloadProfile = async (): Promise<void> => {
    if (!client) return;
    const { data, error: profileError } = await client.auth.getSession();
    if (profileError) {
      setError(profileError.message);
      return;
    }

    const currentSession = data.session;
    setSession(currentSession);
    if (!currentSession) {
      setProfile(null);
      return;
    }

    const { data: currentProfile, error: queryError } = await client
      .from('portal_profiles')
      .select('id, email, username, full_name, role, account_status, rejection_reason')
      .eq('id', currentSession.user.id)
      .single();

    if (queryError) {
      setError(queryError.message);
      setProfile(null);
      return;
    }

    setError('');
    setProfile(currentProfile as PortalProfile);
  };

  useEffect(() => {
    if (!client) return;
    let active = true;

    const loadSession = async (currentSession: Session | null): Promise<void> => {
      if (!active) return;
      setSession(currentSession);
      setError('');

      if (!currentSession) {
        setProfile(null);
        setLoading(false);
        return;
      }

      const { data, error: queryError } = await client
        .from('portal_profiles')
        .select('id, email, username, full_name, role, account_status, rejection_reason')
        .eq('id', currentSession.user.id)
        .single();

      if (!active) return;
      setProfile(data as PortalProfile | null);
      setError(queryError?.message ?? '');
      setLoading(false);
    };

    const { data: authListener } = client.auth.onAuthStateChange((_event, currentSession) => {
      window.setTimeout(() => void loadSession(currentSession), 0);
    });
    void client.auth.getSession().then(({ data, error: sessionError }) => {
      if (sessionError) setError(sessionError.message);
      return loadSession(data.session);
    });

    return () => {
      active = false;
      authListener.subscription.unsubscribe();
    };
  }, [client]);

  const value: PortalContextValue = {
    client,
    session,
    profile,
    loading,
    configurationMissing,
    error,
    reloadProfile,
  };

  return (
    <PortalContext.Provider value={value}>
      <div className="min-h-screen bg-slate-50 text-slate-800">
        <Suspense fallback={<div className="p-8 text-sm text-slate-600">Loading portal...</div>}>
          <Routes>
            <Route path="login" element={<LoginPage />} />
            <Route path="register" element={<RegisterPage />} />
            <Route path="forgot-password" element={<ForgotPasswordPage />} />
            <Route path="reset-password" element={<ResetPasswordPage />} />
            <Route path="admin/events" element={<AdminEventsPage />} />
            <Route path="admin/accounts" element={<AdminAccountsPage />} />
            <Route path="admin/content" element={<AdminContentPage />} />
            <Route path="admin/elections" element={<AdminElectionsPage />} />
            <Route path="admin/suggestions" element={<AdminSuggestionsPage />} />
            <Route path="student" element={<StudentWorkspace />} />
            <Route index element={<PortalDashboard />} />
            <Route path="*" element={<Navigate to="/portal" replace />} />
          </Routes>
        </Suspense>
      </div>
    </PortalContext.Provider>
  );
};

const PortalFrame: React.FC<{ children: React.ReactNode }> = ({ children }) => (
  <main className="mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10 sm:px-6">
    <div className="grid w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm md:grid-cols-[0.85fr_1.15fr]">
      <aside className="flex min-h-56 flex-col justify-between bg-emerald-950 p-7 text-white sm:p-10">
        <Link to="/" className="inline-flex w-fit items-center gap-2 text-sm font-semibold text-emerald-100 hover:text-white">
          <ArrowLeft className="h-4 w-4" /> ACSES website
        </Link>
        <div className="mt-8">
          <p className="text-xs font-bold uppercase text-amber-300">ACSES</p>
          <h1 className="mt-2 text-3xl font-bold">Student portal</h1>
          <p className="mt-3 max-w-sm text-sm leading-6 text-emerald-100">Sign in with your approved student or administrator account.</p>
        </div>
        <p className="mt-8 text-xs text-emerald-200">Secure access powered by Supabase Auth</p>
      </aside>
      <section className="p-6 sm:p-10">{children}</section>
    </div>
  </main>
);

const ConfigurationNotice: React.FC = () => (
  <div className="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950">
    <p className="font-semibold">Supabase is not configured for this build.</p>
    <p className="mt-2">Set <code>VITE_SUPABASE_URL</code> and <code>VITE_SUPABASE_ANON_KEY</code> in the build environment, then rebuild the site.</p>
  </div>
);

const LoginPage: React.FC = () => {
  const { client, configurationMissing, session } = usePortal();
  const navigate = useNavigate();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [message, setMessage] = useState('');
  const [busy, setBusy] = useState(false);

  if (session) return <Navigate to="/portal" replace />;

  const signIn = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    if (!client) return;
    setBusy(true);
    setMessage('');
    const { error } = await client.auth.signInWithPassword({ email, password });
    setBusy(false);
    if (error) setMessage(error.message);
    else navigate('/portal');
  };

  const sendElectionLink = async (): Promise<void> => {
    if (!client || !email) {
      setMessage('Enter the email address for your existing account first.');
      return;
    }
    setBusy(true);
    setMessage('');
    const { error } = await client.auth.signInWithOtp({
      email,
      options: {
        shouldCreateUser: false,
        emailRedirectTo: `${window.location.origin}/portal/student`,
      },
    });
    setBusy(false);
    setMessage(error?.message ?? 'Check your email for a secure sign-in link.');
  };

  return (
    <PortalFrame>
      <div className="mx-auto max-w-md">
        <p className="text-xs font-bold uppercase text-emerald-800">Account access</p>
        <h2 className="mt-2 text-2xl font-bold text-slate-900">Sign in</h2>
        <p className="mt-2 text-sm text-slate-600">Use the email address and password for your portal account.</p>
        {configurationMissing ? <div className="mt-6"><ConfigurationNotice /></div> : (
          <form className="mt-7 space-y-4" onSubmit={signIn}>
            <label className="block text-sm font-medium text-slate-700">Email
              <input type="email" autoComplete="email" required value={email} onChange={(event) => setEmail(event.target.value)} className="mt-1.5 h-11 w-full rounded-md border border-slate-300 px-3 outline-none focus:border-emerald-800 focus:ring-2 focus:ring-emerald-800/15" />
            </label>
            <label className="block text-sm font-medium text-slate-700">Password
              <input type="password" autoComplete="current-password" required value={password} onChange={(event) => setPassword(event.target.value)} className="mt-1.5 h-11 w-full rounded-md border border-slate-300 px-3 outline-none focus:border-emerald-800 focus:ring-2 focus:ring-emerald-800/15" />
            </label>
            {message && <p role="alert" className="text-sm text-rose-700">{message}</p>}
            <button type="submit" disabled={busy} className="inline-flex h-11 w-full items-center justify-center gap-2 rounded-md bg-emerald-900 px-4 font-semibold text-white hover:bg-emerald-800 disabled:opacity-60">
              {busy && <LoaderCircle className="h-4 w-4 animate-spin" />}
              {busy ? 'Signing in...' : 'Sign in'}
            </button>
            <button type="button" disabled={busy} onClick={() => void sendElectionLink()} className="h-10 w-full rounded-md border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-60">Email me an election sign-in link</button>
          </form>
        )}
        {!configurationMissing && <p className="mt-4 text-sm text-slate-600"><Link to="/portal/forgot-password" className="font-semibold text-emerald-800 hover:underline">Forgot your password?</Link></p>}
        <p className="mt-6 text-sm text-slate-600">New student? <Link to="/portal/register" className="font-semibold text-emerald-800 hover:underline">Request an account</Link></p>
      </div>
    </PortalFrame>
  );
};

const ForgotPasswordPage: React.FC = () => {
  const { client, configurationMissing } = usePortal();
  const [email, setEmail] = useState('');
  const [message, setMessage] = useState('');
  const [busy, setBusy] = useState(false);

  const sendReset = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    if (!client) return;
    setBusy(true);
    const { error } = await client.auth.resetPasswordForEmail(email, {
      redirectTo: `${window.location.origin}/portal/reset-password`,
    });
    setBusy(false);
    setMessage(error?.message ?? 'If this email belongs to an account, a password reset link is on its way.');
  };

  return <PortalFrame><div className="mx-auto max-w-md"><p className="text-xs font-bold uppercase text-emerald-800">Account recovery</p><h2 className="mt-2 text-2xl font-bold text-slate-900">Reset password</h2>{configurationMissing ? <div className="mt-6"><ConfigurationNotice /></div> : <form onSubmit={sendReset} className="mt-6 space-y-4"><label className="block text-sm font-medium text-slate-700">Account email<input type="email" required value={email} onChange={(event) => setEmail(event.target.value)} className="mt-1 h-11 w-full rounded-md border border-slate-300 px-3" /></label>{message && <p role="status" className="text-sm text-emerald-800">{message}</p>}<button disabled={busy} className="h-11 w-full rounded-md bg-emerald-900 px-4 font-semibold text-white disabled:opacity-60">{busy ? 'Sending...' : 'Send reset link'}</button></form>}<p className="mt-5 text-sm text-slate-600"><Link to="/portal/login" className="font-semibold text-emerald-800 hover:underline">Back to sign in</Link></p></div></PortalFrame>;
};

const ResetPasswordPage: React.FC = () => {
  const { client, configurationMissing, session } = usePortal();
  const navigate = useNavigate();
  const [password, setPassword] = useState('');
  const [message, setMessage] = useState('');
  const [busy, setBusy] = useState(false);

  const updatePassword = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    if (!client) return;
    setBusy(true);
    const { error } = await client.auth.updateUser({ password });
    setBusy(false);
    if (error) setMessage(error.message);
    else navigate('/portal');
  };

  return <PortalFrame><div className="mx-auto max-w-md"><p className="text-xs font-bold uppercase text-emerald-800">Account recovery</p><h2 className="mt-2 text-2xl font-bold text-slate-900">Choose a new password</h2>{configurationMissing ? <div className="mt-6"><ConfigurationNotice /></div> : !session ? <p className="mt-4 text-sm text-slate-600">Open the reset link from your email on this device, or request a new one.</p> : <form onSubmit={updatePassword} className="mt-6 space-y-4"><label className="block text-sm font-medium text-slate-700">New password<input type="password" autoComplete="new-password" minLength={8} required value={password} onChange={(event) => setPassword(event.target.value)} className="mt-1 h-11 w-full rounded-md border border-slate-300 px-3" /></label>{message && <p role="alert" className="text-sm text-rose-700">{message}</p>}<button disabled={busy} className="h-11 w-full rounded-md bg-emerald-900 px-4 font-semibold text-white disabled:opacity-60">{busy ? 'Updating...' : 'Update password'}</button></form>}<p className="mt-5 text-sm text-slate-600"><Link to="/portal/forgot-password" className="font-semibold text-emerald-800 hover:underline">Request another reset link</Link></p></div></PortalFrame>;
};

const RegisterPage: React.FC = () => {
  const { client, configurationMissing, session } = usePortal();
  const navigate = useNavigate();
  const [form, setForm] = useState({ fullName: '', username: '', email: '', indexNumber: '', className: 'Computer Science', year: '1', password: '' });
  const [message, setMessage] = useState('');
  const [busy, setBusy] = useState(false);

  if (session) return <Navigate to="/portal" replace />;

  const update = (field: keyof typeof form, value: string): void => setForm((current) => ({ ...current, [field]: value }));
  const register = async (event: React.FormEvent<HTMLFormElement>): Promise<void> => {
    event.preventDefault();
    if (!client) return;
    setBusy(true);
    setMessage('');
    const { data, error } = await client.auth.signUp({
      email: form.email,
      password: form.password,
      options: {
        data: {
          full_name: form.fullName,
          username: form.username,
          index_number: form.indexNumber,
          class_name: form.className,
          year: form.year,
        },
      },
    });
    setBusy(false);
    if (error) {
      setMessage(error.message);
    } else if (data.session) {
      navigate('/portal');
    } else {
      setMessage('Check your email to confirm your address. Your account will then be reviewed by an administrator.');
    }
  };

  return (
    <PortalFrame>
      <div className="mx-auto max-w-md">
        <p className="text-xs font-bold uppercase text-emerald-800">Student registration</p>
        <h2 className="mt-2 text-2xl font-bold text-slate-900">Request an account</h2>
        {configurationMissing ? <div className="mt-6"><ConfigurationNotice /></div> : (
          <form className="mt-6 grid gap-3 sm:grid-cols-2" onSubmit={register}>
            <label className="text-sm font-medium text-slate-700 sm:col-span-2">Full name<input required value={form.fullName} onChange={(event) => update('fullName', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <label className="text-sm font-medium text-slate-700">Username<input required value={form.username} onChange={(event) => update('username', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <label className="text-sm font-medium text-slate-700">Index number<input required value={form.indexNumber} onChange={(event) => update('indexNumber', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <label className="text-sm font-medium text-slate-700 sm:col-span-2">Email<input type="email" autoComplete="email" required value={form.email} onChange={(event) => update('email', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            <label className="text-sm font-medium text-slate-700">Programme<select value={form.className} onChange={(event) => update('className', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3"><option>Computer Science</option><option>Cyber Security</option><option>Information System</option></select></label>
            <label className="text-sm font-medium text-slate-700">Year<select value={form.year} onChange={(event) => update('year', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3">{['1', '2', '3', '4'].map((year) => <option key={year} value={year}>{year}</option>)}</select></label>
            <label className="text-sm font-medium text-slate-700 sm:col-span-2">Password<input type="password" autoComplete="new-password" minLength={8} required value={form.password} onChange={(event) => update('password', event.target.value)} className="mt-1 h-10 w-full rounded-md border border-slate-300 px-3" /></label>
            {message && <p role="status" className="text-sm text-emerald-800 sm:col-span-2">{message}</p>}
            <button type="submit" disabled={busy} className="h-11 rounded-md bg-emerald-900 px-4 font-semibold text-white hover:bg-emerald-800 disabled:opacity-60 sm:col-span-2">{busy ? 'Submitting...' : 'Submit account request'}</button>
          </form>
        )}
        <p className="mt-5 text-sm text-slate-600">Already registered? <Link to="/portal/login" className="font-semibold text-emerald-800 hover:underline">Sign in</Link></p>
      </div>
    </PortalFrame>
  );
};

const PortalDashboard: React.FC = () => {
  const { client, configurationMissing, loading, session, profile, error } = usePortal();
  const navigate = useNavigate();

  if (configurationMissing) return <PortalFrame><ConfigurationNotice /></PortalFrame>;
  if (loading) return <PortalFrame><div className="flex items-center gap-3 text-sm text-slate-600"><LoaderCircle className="h-5 w-5 animate-spin" />Loading account...</div></PortalFrame>;
  if (!session) return <Navigate to="/portal/login" replace />;
  if (error || !profile) return <PortalFrame><p role="alert" className="text-sm text-rose-700">{error || 'Your profile could not be loaded. Check the Supabase schema and profile trigger.'}</p></PortalFrame>;
  if (profile.account_status !== 'approved') {
    return <PortalFrame><div className="mx-auto max-w-md"><ShieldCheck className="h-8 w-8 text-amber-700" /><h2 className="mt-4 text-xl font-bold text-slate-900">Account review pending</h2><p className="mt-2 text-sm text-slate-600">Your email is confirmed. An administrator must approve your portal access.</p>{profile.account_status === 'rejected' && profile.rejection_reason && <p className="mt-3 rounded-md bg-rose-50 p-3 text-sm text-rose-800">{profile.rejection_reason}</p>}<button onClick={() => void client?.auth.signOut()} className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-emerald-800"><LogOut className="h-4 w-4" />Sign out</button></div></PortalFrame>;
  }

  return (
    <div className="min-h-screen">
      <header className="border-b border-slate-200 bg-white">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6">
          <div><p className="text-xs font-bold uppercase text-emerald-800">ACSES</p><h1 className="text-lg font-bold text-slate-900">{profile.role === 'admin' ? 'Administration' : 'Student portal'}</h1></div>
          <button onClick={async () => { await client?.auth.signOut(); navigate('/portal/login'); }} className="inline-flex items-center gap-2 rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold hover:bg-slate-50"><LogOut className="h-4 w-4" />Sign out</button>
        </div>
      </header>
      <main className="mx-auto max-w-7xl px-4 py-10 sm:px-6">
        <p className="text-sm text-slate-500">{profile.role === 'admin' ? 'Administrator account' : 'Student account'}</p>
        <h2 className="mt-1 text-3xl font-bold text-slate-900">Welcome, {profile.full_name}</h2>
        <p className="mt-3 max-w-2xl text-slate-600">Your account is authenticated through Supabase. Portal data modules are being connected to the protected database.</p>
        <div className="mt-8 flex flex-wrap gap-3">
          {profile.role === 'admin' && <>
            <Link to="/portal/admin/events" className="rounded-md bg-emerald-900 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Manage events</Link>
            <Link to="/portal/admin/accounts" className="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Review accounts</Link>
            <Link to="/portal/admin/content" className="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Manage content</Link>
            <Link to="/portal/admin/elections" className="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Manage elections</Link>
            <Link to="/portal/admin/suggestions" className="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Review messages</Link>
          </>}
          {profile.role === 'student' && <Link to="/portal/student" className="rounded-md bg-emerald-900 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Open student workspace</Link>}
          <Link to="/events" className="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Public events</Link>
          <Link to="/" className="rounded-md bg-emerald-900 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">ACSES website</Link>
        </div>
      </main>
    </div>
  );
};
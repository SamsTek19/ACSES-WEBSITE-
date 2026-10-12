import { useEffect, useState } from 'react';
import { EventItem } from '../types';

type EventsStatus = 'loading' | 'ready' | 'error';

interface SupabaseEvent {
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

const formatEventDate = (date: Date): string => new Intl.DateTimeFormat('en-US', {
  month: 'long',
  day: 'numeric',
  year: 'numeric',
}).format(date);

const formatEventTime = (date: Date): string => new Intl.DateTimeFormat('en-US', {
  hour: 'numeric',
  minute: '2-digit',
}).format(date);

const mapSupabaseEvent = (event: SupabaseEvent): EventItem => {
  const start = event.start_at ? new Date(event.start_at) : null;
  const end = event.end_at ? new Date(event.end_at) : null;
  const unscheduled = start === null;
  const past = !unscheduled && (end ?? start).getTime() < Date.now();
  const sameDay = Boolean(start && end && start.toDateString() === end.toDateString());
  const image = event.banner_path?.startsWith('http')
    ? event.banner_path
    : event.banner_path
      ? `${import.meta.env.VITE_SUPABASE_URL}/storage/v1/object/public/portal-public/${event.banner_path.split('/').map(encodeURIComponent).join('/')}`
      : 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774764/IMG_9040.jpg';

  return {
    id: event.id,
    title: event.title,
    description: event.description ?? '',
    location: event.location ?? 'Location to be announced',
    date: !start
      ? 'To be announced'
      : end && !sameDay
        ? `${formatEventDate(start)} to ${formatEventDate(end)}`
        : formatEventDate(start),
    time: !start
      ? event.time_label ?? 'To be announced'
      : sameDay && end
        ? `${formatEventTime(start)} - ${formatEventTime(end)}`
        : formatEventTime(start),
    category: unscheduled ? 'Unscheduled' : past ? 'Past' : 'Upcoming',
    eventType: event.category,
    memoriesLink: event.memories_link ?? undefined,
    link: event.cta_url ?? undefined,
    image,
    imageAlt: event.banner_alt ?? event.title,
  };
};

export const useEvents = () => {
  const [events, setEvents] = useState<EventItem[]>([]);
  const [status, setStatus] = useState<EventsStatus>('loading');

  useEffect(() => {
    const controller = new AbortController();
    const supabaseUrl = import.meta.env.VITE_SUPABASE_URL?.trim();
    const supabaseKey = import.meta.env.VITE_SUPABASE_ANON_KEY?.trim();

    if (!supabaseUrl || !supabaseKey) {
      setStatus('error');
      return () => controller.abort();
    }

    void import('./supabase')
      .then(({ getSupabaseClient }) => getSupabaseClient()
        .from('portal_events')
        .select('id, title, description, location, start_at, end_at, time_label, category, cta_url, memories_link, banner_path, banner_alt')
        .order('start_at', { ascending: true, nullsFirst: false })
        .abortSignal(controller.signal))
      .then(({ data, error }) => {
          if (controller.signal.aborted) return;
          if (error) throw error;
          setEvents(((data ?? []) as SupabaseEvent[]).map(mapSupabaseEvent));
          setStatus('ready');
      })
      .catch(() => {
        if (!controller.signal.aborted) setStatus('error');
      });

    return () => controller.abort();
  }, []);

  return { events, status };
};
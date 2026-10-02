import { useEffect, useState } from 'react';
import { EventItem } from '../types';

type EventsStatus = 'loading' | 'ready' | 'error';

const eventsApiUrl = import.meta.env.VITE_EVENTS_API_URL
  || 'http://127.0.0.1:8000/api/public/events';

export const useEvents = () => {
  const [events, setEvents] = useState<EventItem[]>([]);
  const [status, setStatus] = useState<EventsStatus>('loading');

  useEffect(() => {
    const controller = new AbortController();

    fetch(eventsApiUrl, { signal: controller.signal, cache: 'no-store' })
      .then((response) => {
        if (!response.ok) {
          throw new Error(`Events request failed with status ${response.status}`);
        }

        return response.json() as Promise<EventItem[]>;
      })
      .then((items) => {
        setEvents(items);
        setStatus('ready');
      })
      .catch(() => {
        if (!controller.signal.aborted) {
          setStatus('error');
        }
      });

    return () => controller.abort();
  }, []);

  return { events, status };
};
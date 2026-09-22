import React, { useState } from 'react';
import { EVENTS } from '../data/mockData';
import { ArrowUpRight, Calendar, Clock, MapPin } from 'lucide-react';

export const Events: React.FC = () => {
  const [filter, setFilter] = useState<'All' | 'Upcoming' | 'Past'>('All');

  const filteredEvents = EVENTS.filter(
    e => filter === 'All' || e.category === filter
  );

  return (
    <div className="py-12 bg-slate-50 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Header */}
        <div className="text-center max-w-2xl mx-auto mb-10">
          <h1 className="text-4xl font-extrabold text-slate-900">Departmental Events</h1>
          <p className="text-slate-600 mt-2">Explore tech talks, hackathons, workshops, and academic conferences.</p>
        </div>

        {/* Filters */}
        <div className="flex justify-center mb-8 space-x-2">
          {(['All', 'Upcoming', 'Past'] as const).map((cat) => (
            <button
              key={cat}
              onClick={() => setFilter(cat)}
              className={`px-5 py-2 rounded-xl text-sm font-semibold transition-all ${
                filter === cat
                  ? 'bg-emerald-800 text-white shadow-md'
                  : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'
              }`}
            >
              {cat} Events
            </button>
          ))}
        </div>

        {/* List */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {filteredEvents.map((event) => (
            <div key={event.id} className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
              <img src={event.image} alt={event.title} className="h-48 w-full object-cover" />
              <div className="p-6 flex-1 flex flex-col justify-between">
                <div>
                  <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold mb-3 ${
                    event.category === 'Upcoming' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'
                  }`}>
                    {event.category}
                  </span>
                  <h3 className="font-bold text-slate-900 text-xl mb-2">{event.title}</h3>
                  <p className="text-slate-600 text-sm mb-4 leading-relaxed">{event.description}</p>
                </div>

                {event.category === 'Past' && event.memoriesLink ? (
                  <a
                    href={event.memoriesLink}
                    target="_blank"
                    rel="noreferrer"
                    className="inline-flex items-center justify-center gap-2 border-t border-slate-100 pt-4 text-sm font-bold text-emerald-800 hover:text-emerald-600"
                  >
                    View memories
                    <ArrowUpRight className="h-4 w-4" />
                  </a>
                ) : (
                  <div className="space-y-2 border-t border-slate-100 pt-4 text-xs text-slate-500">
                    <div className="flex items-center space-x-2"><Calendar className="w-4 h-4 text-emerald-600" /><span>{event.date}</span></div>
                    <div className="flex items-center space-x-2"><Clock className="w-4 h-4 text-emerald-600" /><span>{event.time}</span></div>
                    <div className="flex items-center space-x-2"><MapPin className="w-4 h-4 text-emerald-600" /><span>{event.location}</span></div>
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>

      </div>
    </div>
  );
};
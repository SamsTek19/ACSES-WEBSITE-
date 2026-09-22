import React from 'react';
import { CLUBS } from '../data/mockData';
import { Code } from 'lucide-react';

export const Clubs: React.FC = () => {
  return (
    <div className="py-12 bg-slate-50 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-12">
          <h1 className="text-4xl font-extrabold text-slate-900">Department Clubs & Societies</h1>
          <p className="text-slate-600 mt-2">Join student-run technical organizations to build projects, compete, and gain leadership skills.</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
          {CLUBS.map((club) => (
            <div key={club.id} className="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between">
              <div>
                {club.image && (
                  <img
                    src={club.image}
                    alt={`${club.name} projects`}
                    className="mb-6 h-48 w-full rounded-xl object-cover"
                  />
                )}
                <div className="flex items-center mb-4">
                  <div className="w-12 h-12 bg-emerald-800 text-white rounded-xl flex items-center justify-center">
                    <Code className="w-6 h-6" />
                  </div>
                </div>

                <h3 className="text-2xl font-bold text-slate-900 mb-1">{club.name}</h3>
                <p className="text-xs text-slate-500 font-medium mb-4">Lead: {club.lead}</p>
                <p className="text-slate-600 text-sm leading-relaxed mb-6">{club.description}</p>
              </div>

              <div>
                <div className="flex flex-wrap gap-2 mb-6">
                  {club.tags.map((t) => (
                    <span key={t} className="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-semibold rounded-md border border-slate-200">
                      {t}
                    </span>
                  ))}
                </div>
                <button className="w-full py-2.5 bg-emerald-800 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition-colors">
                  Join Club
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};
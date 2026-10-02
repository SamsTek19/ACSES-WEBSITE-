import React from 'react';
import { EXECUTIVES } from '../data/mockData';
import { Mail, Link2, Globe } from 'lucide-react';

export const Executives: React.FC = () => {
  return (
    <div className="py-12 bg-slate-50 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-12">
          <h1 className="text-4xl font-extrabold text-slate-900">Department Leadership & Executives</h1>
          <p className="text-slate-600 mt-2">Meet the faculty leaders and student executive committee driving departmental success.</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {EXECUTIVES.map((exec) => {
            const isEmmanuelEffah = exec.name === 'Dr. Emmanuel Effah';

            return (
              <div key={exec.id} className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col justify-between">
                <div>
                  <img
                    src={exec.image}
                    alt={exec.name}
                    className="h-64 w-full object-cover"
                    style={isEmmanuelEffah ? { objectPosition: 'center 5%' } : undefined}
                  />
                  <div className="p-6">
                    <span className="text-xs font-bold text-emerald-700 uppercase tracking-wider">{exec.role}</span>
                    <h3 className="font-bold text-slate-900 text-xl mt-1">{exec.name}</h3>
                    <p className="text-slate-600 text-xs mt-3 leading-relaxed">{exec.bio}</p>
                  </div>
                </div>

                <div className="px-6 pb-6 pt-2 border-t border-slate-100 flex items-center justify-between text-slate-500">
                  <a href={`mailto:${exec.email}`} className="hover:text-emerald-700 transition-colors">
                    <Mail className="w-5 h-5" />
                  </a>
                  <div className="flex space-x-3">
                    {exec.linkedin && (
                      <a href={exec.linkedin} target="_blank" rel="noreferrer" className="hover:text-emerald-700 transition-colors">
                        <Link2 className="w-5 h-5" />
                      </a>
                    )}
                    {exec.github && (
                      <a href={exec.github} target="_blank" rel="noreferrer" className="hover:text-emerald-700 transition-colors">
                        <Globe className="w-5 h-5" />
                      </a>
                    )}
                  </div>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
};
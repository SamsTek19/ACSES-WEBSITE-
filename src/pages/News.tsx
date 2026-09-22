import React from 'react';
import { NEWS } from '../data/mockData';

export const News: React.FC = () => {
  return (
    <div className="py-12 bg-slate-50 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-12">
          <h1 className="text-4xl font-extrabold text-slate-900">News & Announcements</h1>
          <p className="text-slate-600 mt-2">Latest updates, achievements, research breakthroughs, and academic notices.</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {NEWS.map((item) => (
            <div key={item.id} className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
              <img src={item.image} alt={item.title} className="h-52 w-full object-cover" />
              <div className="p-6 flex-1 flex flex-col justify-between">
                <div>
                  <span className="text-xs font-bold text-emerald-700 uppercase tracking-wider">{item.category}</span>
                  <h3 className="font-bold text-slate-900 text-xl mt-2 mb-3 leading-snug">{item.title}</h3>
                  <p className="text-slate-600 text-sm leading-relaxed mb-4">{item.summary}</p>
                </div>
                <div className="border-t border-slate-100 pt-4 flex justify-between text-xs text-slate-400">
                  <span>By {item.author}</span>
                  <span>{item.date}</span>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};
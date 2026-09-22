import React, { useState } from 'react';
import { RESOURCES } from '../data/mockData';
import { Download, FileText, Filter } from 'lucide-react';

export const Resources: React.FC = () => {
  const [selectedCat, setSelectedCat] = useState<string>('All');

  const categories = ['All', 'Syllabus', 'Lab Guides', 'Software', 'Research'];

  const filtered = selectedCat === 'All' 
    ? RESOURCES 
    : RESOURCES.filter(r => r.category === selectedCat);

  return (
    <div className="py-12 bg-slate-50 min-h-screen">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-10">
          <h1 className="text-4xl font-extrabold text-slate-900">Academic & Lab Resources</h1>
          <p className="text-slate-600 mt-2">Download official course handbooks, software configuration guides, and thesis guidelines.</p>
        </div>

        {/* Filter Pills */}
        <div className="flex flex-wrap justify-center gap-2 mb-10">
          {categories.map((cat) => (
            <button
              key={cat}
              onClick={() => setSelectedCat(cat)}
              className={`px-4 py-2 rounded-xl text-xs font-semibold transition-all ${
                selectedCat === cat
                  ? 'bg-emerald-800 text-white shadow-md'
                  : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'
              }`}
            >
              {cat}
            </button>
          ))}
        </div>

        {/* Resource List */}
        <div className="space-y-4 max-w-4xl mx-auto">
          {filtered.map((res) => (
            <div key={res.id} className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <div className="flex items-start space-x-4">
                <div className="w-12 h-12 bg-emerald-50 text-emerald-800 rounded-xl flex items-center justify-center flex-shrink-0">
                  <FileText className="w-6 h-6" />
                </div>
                <div>
                  <div className="flex items-center space-x-2">
                    <span className="text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded">
                      {res.category}
                    </span>
                    {res.fileSize && <span className="text-xs text-slate-400">• {res.fileSize}</span>}
                  </div>
                  <h3 className="font-bold text-slate-900 text-base mt-1">{res.title}</h3>
                  <p className="text-xs text-slate-500 mt-1">{res.description}</p>
                </div>
              </div>

              <a
                href={res.link}
                className="w-full sm:w-auto px-4 py-2.5 bg-emerald-800 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold flex items-center justify-center space-x-2 transition-colors flex-shrink-0"
              >
                <Download className="w-4 h-4" />
                <span>Download</span>
              </a>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};
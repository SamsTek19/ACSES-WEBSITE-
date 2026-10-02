import React from 'react';

interface PolicySection {
  heading: string;
  paragraphs: string[];
}

interface LegalPageProps {
  title: string;
  intro: string;
  sections: PolicySection[];
}

export const LegalPage: React.FC<LegalPageProps> = ({ title, intro, sections }) => {
  return (
    <div className="bg-slate-50 min-h-screen py-16">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="bg-emerald-800 px-6 py-8 sm:px-10">
            <p className="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-200">ACSES-UMaT</p>
            <h1 className="mt-3 text-3xl sm:text-4xl font-extrabold text-white">{title}</h1>
          </div>

          <div className="px-6 py-8 sm:px-10 sm:py-10">
            <p className="text-base leading-7 text-slate-600">{intro}</p>

            <div className="mt-8 space-y-8">
              {sections.map((section) => (
                <section key={section.heading} className="border-b border-slate-200 pb-6 last:border-b-0 last:pb-0">
                  <h2 className="text-xl font-bold text-slate-900">{section.heading}</h2>
                  <div className="mt-3 space-y-3">
                    {section.paragraphs.map((paragraph, index) => (
                      <p key={`${section.heading}-${index}`} className="text-sm leading-7 text-slate-600">
                        {paragraph}
                      </p>
                    ))}
                  </div>
                </section>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

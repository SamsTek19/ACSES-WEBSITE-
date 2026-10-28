import React from 'react';
import { motion } from 'framer-motion';
import { MapPin, Navigation, Building2 } from 'lucide-react';

export const MapSection: React.FC = () => {
  return (
    <section className="relative isolate overflow-hidden py-16 bg-slate-50 border-t border-slate-200">
      <motion.div
        aria-hidden="true"
        animate={{ y: [0, -12, 0], rotate: [0, 5, 0] }}
        transition={{ duration: 15, repeat: Infinity, ease: 'easeInOut' }}
        className="pointer-events-none absolute right-[8%] top-12 text-emerald-950/[0.055]"
      >
        <MapPin className="h-20 w-20" strokeWidth={1.2} />
      </motion.div>
      <motion.div
        initial={{ opacity: 0, y: 24, scale: 0.985 }}
        whileInView={{ opacity: 1, y: 0, scale: 1 }}
        viewport={{ once: true, amount: 0.15 }}
        transition={{ duration: 0.55, ease: 'easeOut' }}
        className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
      >
        <motion.div
          initial={{ opacity: 0, y: 18 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true, amount: 0.4 }}
          transition={{ duration: 0.5, ease: 'easeOut' }}
          className="text-center max-w-2xl mx-auto mb-10"
        >
          <span className="text-emerald-700 font-semibold text-sm uppercase tracking-wider">Locate Us</span>
          <h2 className="text-3xl font-extrabold text-slate-900 mt-2">Department Location</h2>
          <p className="text-slate-600 mt-2">
            Visit our state-of-the-art academic complex, computational labs, and faculty offices.
          </p>
        </motion.div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 items-stretch rounded-2xl overflow-hidden shadow-lg bg-white border border-slate-200">
          {/* Map Details Panel */}
          <div className="p-8 flex flex-col justify-between bg-emerald-900 text-white">
            <div>
              <div className="flex items-center space-x-3 text-emerald-300 mb-6">
                <Building2 className="w-8 h-8" />
                <h3 className="text-xl font-bold text-white">Engineering Block C</h3>
              </div>

              <div className="space-y-6 text-sm">
                <div className="flex items-start space-x-3">
                  <MapPin className="w-5 h-5 text-emerald-400 mt-1 flex-shrink-0" />
                  <div>
                    <strong className="block text-white font-semibold">Campus Address:</strong>
                    <p className="text-emerald-100">
                      Department of Computer Science & Engineering<br />
                      University of Mines and Technology (UMaT)<br />
                      Main Campus, Tarkwa, Ghanak,
                    </p>
                  </div>
                </div>

                <div className="flex items-start space-x-3">
                  <Navigation className="w-5 h-5 text-emerald-400 mt-1 flex-shrink-0" />
                  <div>
                    <strong className="block text-white font-semibold">Landmarks:</strong>
                    <p className="text-emerald-100">Opposite Main Entrance, Adjacent to Old Administration Building.</p>
                  </div>
                </div>
              </div>
            </div>

            <div className="mt-8 pt-6 border-t border-emerald-800">
              <span className="text-xs text-emerald-300 block">Visiting Hours</span>
              <span className="text-sm font-medium text-white">Mon – Fri: 8:00 AM – 6:00 PM</span>
            </div>
          </div>

          {/* Interactive Google Map Iframe */}
          <div className="lg:col-span-2 min-h-[350px] relative w-full h-full bg-slate-200">
            <iframe
              title="Department Location Map"
              src="https://www.google.com/maps?q=UMaT+Computer+Science+and+Engineering+Dept%2C+Ghana&ll=5.2982283%2C-2.0004077&z=17&output=embed"
              className="w-full h-full border-0 min-h-[380px]"
              allowFullScreen={false}
              loading="lazy"
              referrerPolicy="no-referrer-when-downgrade"
            ></iframe>
          </div>
        </div>
      </motion.div>
    </section>
  );
};
import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { 
  ArrowRight, Users, Calendar, Newspaper, BookOpen, Send, 
  CheckCircle2, Code2, Bot, ShieldCheck, Sparkles 
} from 'lucide-react';
import { NEWS, CLUBS, RESOURCES } from '../data/mockData';
import { MapSection } from '../components/MapSection';
import { useEvents } from '../lib/useEvents';

const heroImages = [
  {
    src: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789776683/IMG_9036.jpg',
    alt: 'ACSES students collaborating',
  },
  {
    src: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789777794/IMG_9065.jpg',
    alt: 'ACSES student activity',
  },
  {
    src: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774764/IMG_9040.jpg',
    alt: 'ACSES technology event',
  },
  {
    src: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774759/IMG_9377.jpg',
    alt: 'ACSES technology students',
  },
];

export const Home: React.FC = () => {
  const { events, status: eventsStatus } = useEvents();
  const [formData, setFormData] = useState({ name: '', email: '', subject: '', message: '' });
  const [formSubmitted, setFormSubmitted] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [submitError, setSubmitError] = useState('');
  const [heroImageIndex, setHeroImageIndex] = useState(0);
  const contactApiUrl = import.meta.env.VITE_CONTACT_API_URL || 'http://127.0.0.1:8000/api/public/contact';

  useEffect(() => {
    const imageRotation = window.setInterval(() => {
      setHeroImageIndex((currentIndex) => (currentIndex + 1) % heroImages.length);
    }, 2500);

    return () => window.clearInterval(imageRotation);
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitError('');
    setIsSubmitting(true);

    try {
      const response = await fetch(contactApiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(formData),
      });

      if (!response.ok) {
        let errorMessage = 'Unable to send your message right now. Please try again later.';

        try {
          const payload = await response.json();
          errorMessage = payload.message || errorMessage;
        } catch {
          // Ignore JSON decode issues and keep the fallback message.
        }

        throw new Error(errorMessage);
      }

      setFormSubmitted(true);
      setFormData({ name: '', email: '', subject: '', message: '' });
      window.setTimeout(() => setFormSubmitted(false), 5000);
    } catch (error) {
      setSubmitError(error instanceof Error ? error.message : 'Unable to send your message right now. Please try again later.');
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="space-y-0">
      {/* Hero Section */}
      <section className="relative bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-900 text-white overflow-hidden py-20 lg:py-28">
        <div className="absolute inset-0 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:24px_24px] opacity-10"></div>
        
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            
            {/* Hero Text */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6 }}
              className="space-y-6"
            >
              <div className="inline-flex items-center space-x-2 bg-emerald-800/60 border border-emerald-500/30 px-3.5 py-1.5 rounded-full text-xs font-semibold text-emerald-300">
                <img src="/acses-logo.png" alt="ACSES logo" className="h-7 w-7 rounded-md bg-white object-contain" />
                <span>Welcome to ACSES Official</span>
              </div>
              <h1 className="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                Empowering Minds, <br />
                <span className="text-emerald-400">Engineering Futures.</span>
              </h1>
              <p className="text-slate-300 text-lg max-w-xl leading-relaxed">
                Welcome to the Association Of Computer Science & Engineering Students. We foster innovation in  Information Systems and Technology, Cyber Security, and Robotics.
              </p>

              <div className="flex flex-wrap gap-4 pt-4">
                <Link
                  to="/executives"
                  className="px-6 py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold rounded-xl shadow-lg shadow-emerald-500/25 transition-all flex items-center space-x-2"
                >
                  <span>Know Your Executives</span>
                  <ArrowRight className="w-5 h-5" />
                </Link>
                <Link
                  to="/resources"
                  className="px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl border border-white/20 transition-all"
                >
                  Explore Resources
                </Link>
              </div>
            </motion.div>

            {/* Hero Images Grid */}
            <motion.div
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="grid grid-cols-2 gap-4 relative"
            >
              <div className="space-y-4">
                <img
                  src={heroImages[heroImageIndex].src}
                  alt={heroImages[heroImageIndex].alt}
                  className="rounded-2xl shadow-2xl object-cover h-48 sm:h-64 w-full border-2 border-emerald-500/20"
                />
                <div className="bg-emerald-800/80 backdrop-blur-md p-4 rounded-2xl border border-emerald-400/30">
                  <p className="text-2xl font-bold text-white">ACSES</p>
                  <p className="text-xs text-emerald-200">The Future Of Technology</p>
                </div>
              </div>
              <div className="space-y-4 pt-8">
                <div className="bg-emerald-800/80 backdrop-blur-md p-4 rounded-2xl border border-emerald-400/30">
                  <p className="text-2xl font-bold text-white">8+</p>
                  <p className="text-xs text-emerald-200">Active Clubs</p>
                </div>
                <img
                  src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=600"
                  alt="Robotics project engineering"
                  className="rounded-2xl shadow-2xl object-cover h-48 sm:h-64 w-full border-2 border-emerald-500/20"
                />
              </div>
            </motion.div>

          </div>
        </div>
      </section>

      {/* Events Section */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
              <span className="text-emerald-700 font-semibold text-sm uppercase tracking-wider">Department Activities</span>
              <h2 className="text-3xl font-extrabold text-slate-900 mt-1">Upcoming & Past Events</h2>
            </div>
            <Link to="/events" className="mt-4 md:mt-0 text-emerald-700 font-semibold hover:text-emerald-800 flex items-center">
              View All Events <ArrowRight className="w-4 h-4 ml-1" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {eventsStatus === 'loading' && <p className="md:col-span-3 text-center text-slate-500">Loading events...</p>}
            {eventsStatus === 'error' && <p className="md:col-span-3 text-center text-slate-500">Events are temporarily unavailable.</p>}
            {eventsStatus === 'ready' && events.length === 0 && <p className="md:col-span-3 text-center text-slate-500">No events have been published yet.</p>}
            {events.slice(0, 3).map((event) => (
              <div key={event.id} className="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col">
                <img src={event.image} alt={event.imageAlt ?? event.title} className="h-48 w-full object-cover" />
                <div className="p-6 flex-1 flex flex-col justify-between">
                  <div>
                    <span className={`inline-block px-3 py-1 rounded-full text-xs font-semibold mb-3 ${
                      event.category === 'Upcoming' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700'
                    }`}>
                      {event.category}
                    </span>
                    {event.eventType && <p className="text-xs font-semibold uppercase text-emerald-700 mb-2">{event.eventType}</p>}
                    <h3 className="font-bold text-slate-900 text-lg mb-2">{event.title}</h3>
                    <p className="text-slate-600 text-sm line-clamp-2 mb-4">{event.description}</p>
                  </div>
                  {event.category === 'Past' && event.memoriesLink ? (
                    <a href={event.memoriesLink} target="_blank" rel="noreferrer" className="inline-flex items-center gap-2 border-t border-slate-100 pt-4 text-sm font-bold text-emerald-800 hover:text-emerald-600">
                      View memories
                      <ArrowRight className="h-4 w-4" />
                    </a>
                  ) : (
                    <div className="pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-1">
                      <div className="flex items-center space-x-2"><Calendar className="w-3.5 h-3.5 text-emerald-600" /><span>{event.date} • {event.time}</span></div>
                      {event.link && <a href={event.link} target="_blank" rel="noreferrer" className="inline-flex pt-2 font-semibold text-emerald-800 hover:text-emerald-600">More details <ArrowRight className="w-3.5 h-3.5 ml-1" /></a>}
                    </div>
                  )}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* News Section */}
      <section className="py-20 bg-slate-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
              <span className="text-emerald-700 font-semibold text-sm uppercase tracking-wider">Stay Informed</span>
              <h2 className="text-3xl font-extrabold text-slate-900 mt-1">Latest News & Announcements</h2>
            </div>
            <Link to="/news" className="mt-4 md:mt-0 text-emerald-700 font-semibold hover:text-emerald-800 flex items-center">
              View All News <ArrowRight className="w-4 h-4 ml-1" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {NEWS.slice(0, 3).map((item) => (
              <div key={item.id} className="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col">
                <img src={item.image} alt={item.title} className="h-44 w-full object-cover" />
                <div className="p-6 flex-1 flex flex-col justify-between">
                  <div>
                    <span className="text-xs font-semibold text-emerald-700 uppercase tracking-wider">{item.category}</span>
                    <h3 className="font-bold text-slate-900 text-lg mt-1 mb-2 leading-snug">{item.title}</h3>
                    <p className="text-slate-600 text-sm line-clamp-2 mb-4">{item.summary}</p>
                  </div>
                  <div className="text-xs text-slate-400 border-t border-slate-100 pt-3 flex justify-between">
                    <span>{item.author}</span>
                    <span>{item.date}</span>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Clubs Section */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-2xl mx-auto mb-12">
            <span className="text-emerald-700 font-semibold text-sm uppercase tracking-wider">Student Life</span>
            <h2 className="text-3xl font-extrabold text-slate-900 mt-1">Student Communities & Clubs</h2>
            <p className="text-slate-600 mt-2">Get involved in student-led technical organizations, hackathons, and collaborative research teams.</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {CLUBS.slice(0, 3).map((club) => (
              <div key={club.id} className="p-6 bg-emerald-50/50 rounded-2xl border border-emerald-100 hover:border-emerald-300 transition-colors">
                {club.image && (
                  <img
                    src={club.image}
                    alt={`${club.name} projects`}
                    className="mb-5 h-44 w-full rounded-xl object-cover"
                  />
                )}
                <div className="w-12 h-12 bg-emerald-800 text-white rounded-xl flex items-center justify-center mb-4">
                  <Code2 className="w-6 h-6" />
                </div>
                <h3 className="font-bold text-slate-900 text-lg mb-1">{club.name}</h3>
                <p className="text-slate-600 text-xs leading-relaxed mb-4">{club.description}</p>
                <div className="flex flex-wrap gap-1.5">
                  {club.tags.map(t => (
                    <span key={t} className="px-2 py-0.5 bg-white border border-emerald-200 text-[10px] font-medium text-emerald-800 rounded">
                      {t}
                    </span>
                  ))}
                </div>
              </div>
            ))}
          </div>
          <div className="mt-10 text-center">
            <Link
              to="/clubs"
              className="inline-flex items-center gap-2 rounded-xl bg-emerald-800 px-6 py-3 font-bold text-white transition-colors hover:bg-emerald-700"
            >
              View all clubs
              <ArrowRight className="h-4 w-4" />
            </Link>
          </div>
        </div>
      </section>

      {/* Resources Section */}
      <section className="py-20 bg-slate-900 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
              <span className="text-emerald-400 font-semibold text-sm uppercase tracking-wider">Academic Support</span>
              <h2 className="text-3xl font-extrabold text-white mt-1">Useful Departmental Resources</h2>
            </div>
            <Link to="/resources" className="mt-4 md:mt-0 text-emerald-400 font-semibold hover:text-emerald-300 flex items-center">
              Browse All Resources <ArrowRight className="w-4 h-4 ml-1" />
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {RESOURCES.slice(0, 4).map((res) => (
              <div key={res.id} className="p-6 bg-slate-800/80 rounded-2xl border border-slate-700 flex justify-between items-start">
                <div className="space-y-2">
                  <span className="px-2.5 py-1 bg-emerald-900/60 text-emerald-300 rounded-md text-xs font-semibold">
                    {res.category}
                  </span>
                  <h3 className="text-lg font-bold text-white">{res.title}</h3>
                  <p className="text-slate-400 text-xs max-w-md">{res.description}</p>
                </div>
                <a
                  href={res.link}
                  className="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-semibold flex items-center space-x-1"
                >
                  <span>Download</span>
                </a>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Get In Touch Section */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="max-w-3xl mx-auto bg-slate-50 p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-sm">
            <div className="text-center mb-8">
              <span className="text-emerald-700 font-semibold text-sm uppercase tracking-wider">Reach Out</span>
              <h2 className="text-3xl font-extrabold text-slate-900 mt-1">Get In Touch</h2>
              <p className="text-slate-600 text-sm mt-2">Have queries regarding admissions, research collaborations, or department activities?</p>
            </div>

            {formSubmitted ? (
              <div className="p-6 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-2xl flex items-center space-x-3">
                <CheckCircle2 className="w-6 h-6 text-emerald-700 flex-shrink-0" />
                <span>Thank you! Your message has been submitted successfully. We will get back to you shortly.</span>
              </div>
            ) : (
              <form onSubmit={handleSubmit} className="space-y-4">
                {submitError && (
                  <div className="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-sm">
                    {submitError}
                  </div>
                )}

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-xs font-semibold text-slate-700 uppercase mb-1">Your Name</label>
                    <input
                      type="text"
                      required
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      placeholder="Samuel Sarfo"
                      className="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-600 focus:outline-none text-slate-900 text-sm"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-semibold text-slate-700 uppercase mb-1">Email Address</label>
                    <input
                      type="email"
                      required
                      value={formData.email}
                      onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                      placeholder="samsTek@example.com"
                      className="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-600 focus:outline-none text-slate-900 text-sm"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-xs font-semibold text-slate-700 uppercase mb-1">Subject</label>
                  <input
                    type="text"
                    required
                    value={formData.subject}
                    onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                    placeholder="Admissions Query / Research Partnership"
                    className="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-600 focus:outline-none text-slate-900 text-sm"
                  />
                </div>

                <div>
                  <label className="block text-xs font-semibold text-slate-700 uppercase mb-1">Message</label>
                  <textarea
                    rows={4}
                    required
                    value={formData.message}
                    onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                    placeholder="Write your message here..."
                    className="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-600 focus:outline-none text-slate-900 text-sm"
                  ></textarea>
                </div>

                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full py-3.5 bg-emerald-800 hover:bg-emerald-700 disabled:bg-emerald-500 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-md transition-colors flex items-center justify-center space-x-2"
                >
                  <Send className="w-4 h-4" />
                  <span>{isSubmitting ? 'Sending...' : 'Send Message'}</span>
                </button>
              </form>
            )}
          </div>
        </div>
      </section>

      {/* Map Section */}
      <MapSection />
    </div>
  );
};
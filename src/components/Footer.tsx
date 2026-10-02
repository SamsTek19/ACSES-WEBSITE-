import React from 'react';
import { Link } from 'react-router-dom';
import { FaInstagram, FaXTwitter, FaTiktok, FaLinkedinIn } from 'react-icons/fa6';
import { Mail, Phone, MapPin } from 'lucide-react';

export const Footer: React.FC = () => {
  return (
    <footer className="bg-slate-900 text-slate-300 pt-16 pb-8 border-t border-slate-800">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-slate-800">
          
          {/* Col 1: Brand */}
          <div className="space-y-4">
            <div className="flex items-center space-x-3">
              <div className="h-10 w-10 overflow-hidden rounded-lg bg-white">
                <img src="/acses-logo.png" alt="ACSES logo" className="h-full w-full object-contain" />
              </div>
              <span className="text-xl font-bold tracking-tight text-white">
                ACSES <span className="text-emerald-500">ASSOCIATION</span>
              </span>
            </div>
            <p className="text-sm text-slate-400 leading-relaxed">
              Connecting, empowering and advancing students in technology. We are the future of technology.
            </p>
            <div className="flex space-x-4 pt-2">
              <a href="https://www.instagram.com/acsesumat" className="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-colors" aria-label="Instagram" target="_blank" rel="noreferrer">
                <FaInstagram className="w-4 h-4" />
              </a>
              <a href="https://x.com/acsesumat" className="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-colors" aria-label="X" target="_blank" rel="noreferrer">
                <FaXTwitter className="w-4 h-4" />
              </a>
              <a href="https://www.tiktok.com/@acsesumat" className="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-colors" aria-label="TikTok" target="_blank" rel="noreferrer">
                <FaTiktok className="w-4 h-4" />
              </a>
              <a href="https://www.linkedin.com/company/acses-umat" className="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-colors" aria-label="LinkedIn" target="_blank" rel="noreferrer">
                <FaLinkedinIn className="w-4 h-4" />
              </a>
            </div>
          </div>

          {/* Col 2: Quick Links */}
          <div>
            <h4 className="text-white font-semibold text-base mb-4 border-l-2 border-emerald-500 pl-3">Quick Navigation</h4>
            <ul className="space-y-2.5 text-sm">
              <li><Link to="Home" className="hover:text-emerald-400 transition-colors">Home</Link></li>
              <li><Link to="Events" className="hover:text-emerald-400 transition-colors">Department Events</Link></li>
              <li><Link to="News" className="hover:text-emerald-400 transition-colors">News & Announcements</Link></li>
              <li><Link to="Clubs" className="hover:text-emerald-400 transition-colors">Student Clubs</Link></li>
              <li><Link to="Resources" className="hover:text-emerald-400 transition-colors">Academic Resources</Link></li>
              <li><Link to="Executives" className="hover:text-emerald-400 transition-colors">Executive Committee</Link></li>
            </ul>
          </div>

          {/* Col 3: Academic Resources */}
          <div>
            <h4 className="text-white font-semibold text-base mb-4 border-l-2 border-emerald-500 pl-3">Popular Links</h4>
            <ul className="space-y-2.5 text-sm">
              <li><a href="Resources" className="hover:text-emerald-400 transition-colors">Course Syllabus</a></li>
              <li><a href="https://www.ieee.org/" className="hover:text-emerald-400 transition-colors">Research Publications</a></li>
              <li><a href="https://lms.umat.edu.gh/login/index.php" className="hover:text-emerald-400 transition-colors">UMaT Virtual Learning Environment</a></li>
              <li><a href="https://student.umat.edu.gh/" className="hover:text-emerald-400 transition-colors">Student Portal</a></li>
              <li><a href="https://www.umat.edu.gh/" className="hover:text-emerald-400 transition-colors">UMaT Website</a></li>
            </ul>
          </div>

          {/* Col 4: Contact info */}
          <div>
            <h4 className="text-white font-semibold text-base mb-4 border-l-2 border-emerald-500 pl-3">Contact</h4>
            <ul className="space-y-3 text-sm text-slate-400">
              <li className="flex items-start space-x-3">
                <MapPin className="w-4 h-4 text-emerald-500 mt-1 shrink-0" />
                <span>Computer Science and Engineering Department</span>
              </li>
              <li className="flex items-center space-x-3">
                <Phone className="w-4 h-4 text-emerald-500 shrink-0" />
                <span>+233 30 212 3456</span>
              </li>
              <li className="flex items-center space-x-3">
                <Mail className="w-4 h-4 text-emerald-500 shrink-0" />
                <span>acses.umat.hq@gmail.com</span>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom bar */}
        <div className="pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500 space-y-4 md:space-y-0">
          <p>© {new Date().getFullYear()} Department of Computer Science & Engineering. All rights reserved.</p>
          <div className="flex space-x-6">
            <Link to="/privacy-policy" className="hover:text-emerald-400 transition-colors">Privacy Policy</Link>
            <Link to="/terms-of-service" className="hover:text-emerald-400 transition-colors">Terms of Service</Link>
            <Link to="/accessibility-policy" className="hover:text-emerald-400 transition-colors">Accessibility Policy</Link>
          </div>
        </div>
      </div>
    </footer>
  );
};
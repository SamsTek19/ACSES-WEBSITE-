import React, { useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, useLocation } from 'react-router-dom';
import { Navbar } from './src/components/Navbar';
import { Footer } from './src/components/Footer';
import { Home } from './src/pages/Home';
import { Events } from './src/pages/Events';
import { News } from './src/pages/News';
import { Clubs } from './src/pages/Clubs';
import { Resources } from './src/pages/Resources';
import { Executives } from './src/pages/Executives';
import { PrivacyPolicyPage } from './src/pages/PrivacyPolicyPage';
import { TermsPage } from './src/pages/TermsPage';
import { AccessibilityPage } from './src/pages/AccessibilityPage';

const ScrollToTop: React.FC = () => {
  const { pathname } = useLocation();

  useEffect(() => {
    window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
  }, [pathname]);

  return null;
};

export const App: React.FC = () => {
  return (
    <Router>
      <ScrollToTop />
      <Routes>
        <Route path="*" element={<PublicSite />} />
      </Routes>
    </Router>
  );
};

const PublicSite: React.FC = () => (
  <div className="min-h-screen flex flex-col bg-white text-slate-800 font-sans antialiased">
    <Navbar />
    <main className="grow">
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/events" element={<Events />} />
        <Route path="/news" element={<News />} />
        <Route path="/clubs" element={<Clubs />} />
        <Route path="/resources" element={<Resources />} />
        <Route path="/executives" element={<Executives />} />
        <Route path="/privacy-policy" element={<PrivacyPolicyPage />} />
        <Route path="/terms-of-service" element={<TermsPage />} />
        <Route path="/accessibility-policy" element={<AccessibilityPage />} />
      </Routes>
    </main>
    <Footer />
  </div>
);

export default App;
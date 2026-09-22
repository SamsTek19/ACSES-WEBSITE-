import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { Navbar } from './src/components/Navbar';
import { Footer } from './src/components/Footer';
import { Home } from './src/pages/Home';
import { Events } from './src/pages/Events';
import { News } from './src/pages/News';
import { Clubs } from './src/pages/Clubs';
import { Resources } from './src/pages/Resources';
import { Executives } from './src/pages/Executives';

export const App: React.FC = () => {
  return (
    <Router>
      <div className="min-h-screen flex flex-col bg-white text-slate-800 font-sans antialiased">
        <Navbar />
        <main className="flex-grow">
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/events" element={<Events />} />
            <Route path="/news" element={<News />} />
            <Route path="/clubs" element={<Clubs />} />
            <Route path="/resources" element={<Resources />} />
            <Route path="/executives" element={<Executives />} />
          </Routes>
        </main>
        <Footer />
      </div>
    </Router>
  );
};

export default App;
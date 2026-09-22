export interface EventItem {
  id: string;
  title: string;
  date: string;
  time: string;
  location: string;
  category: 'Upcoming' | 'Past';
  memoriesLink?: string;
  description: string;
  image: string;
}

export interface NewsItem {
  id: string;
  title: string;
  date: string;
  author: string;
  summary: string;
  category: string;
  image: string;
}

export interface ClubItem {
  id: string;
  name: string;
  lead: string;
  description: string;
  image?: string;
  tags: string[];
  iconName: string;
  joinLink?: string;
}

export interface ResourceItem {
  id: string;
  title: string;
  category: 'Syllabus' | 'Lab Guides' | 'Software' | 'Research';
  fileSize?: string;
  format?: string;
  description: string;
  link: string;
}

export interface Executive {
  id: string;
  name: string;
  role: string;
  bio: string;
  image: string;
  email: string;
  linkedin: string;
  github?: string;
}
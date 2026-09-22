import { EventItem, NewsItem, ClubItem, ResourceItem, Executive } from '../types';

export const EVENTS: EventItem[] = [
    {id: '1',
    title: 'Biometric Registration For Continuing Students',
    date: 'October 12, 2026 to October 14, 2026',
    time: '10:00 AM - 04:00 PM',
    location: 'ACSES Department Office',
    category: 'Upcoming',
    description: 'Students are to register for courses for the upcoming semester.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774764/IMG_9040.jpg'
  },
    {
    id: '2',
    title: 'Freshers Orientation & Medical Examination Of Freshers',
    date: 'October 12, 2026 to October 23, 2026',
    time: '09:00 AM - 05:00 PM',
    location: 'UMaT Main Auditorium',
    category: 'Upcoming',
    description: 'An orientation program for new students, and all first year students are to be introduced to the department, faculty, and student clubs.',
    image: 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=800'
  },
  {
    id: '3',
    title: 'Annual Hackathon 2026: AI for Good',
    date: 'To be announced',
    time: '09:00 AM - 06:00 PM',
    location: 'Main Auditorium',
    category: 'Upcoming',
    description: 'A hackathon bringing together students to solve real-world problems using artificial intelligence and robotics.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774758/IMG_9404.jpg'
  },
  {
    id: '4',
    title: 'Innovation & Research Symposium 2026',
    date: 'To be announced',
    time: 'To be announced',
    location: 'To be announced',
    category: 'Upcoming',
    description: 'Ecpects facilitators, researchers, and students to present their research findings, innovative projects, and technological advancements in a symposium format.',
    image: 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&q=80&w=800'
  },
    {
    id: '5',
    title: 'Annual Hackathon 2025: AI for Good',
    date: 'October 15, 2025',
    time: '09:00 AM - 06:00 PM',
    location: 'Main Auditorium',
    category: 'Past',
    memoriesLink: 'https://drive.google.com/drive/u/0/mobile/folders/18-VTlVSNvyiaA4OnHj6w3jaH8iE8oHU0?usp=drive_link',
    description: 'Last year\'s hackathon brought together students to solve real-world problems using artificial intelligence and robotics.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789777230/IMG_9081.jpg'
  },
    {
    id: '6',
    title: 'ACSES Annual Dinner and Awards Night',
    date: 'October 15, 2026',
    time: '09:00 AM - 06:00 PM',
    location: 'Main Auditorium',
    category: 'Past',
    memoriesLink: 'https://bigmethphotography01.pixieset.com/acsesredcarpet/',
    description: 'Last year\'s annual dinner and awards night celebrated the achievements of students, faculty, and staff in the department.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789777046/IMG_9123.jpg'
  },
    {
    id: '7',
    title: 'Kakalika Freshers Akwaaba Night',
    date: 'October 15, 2026',
    time: '09:00 AM - 06:00 PM',
    location: 'Main Auditorium',
    category: 'Past',
    memoriesLink: 'https://bigmethphotography.pixieset.com/kakalikafreshersakwaaba/',
    description: 'Freshers Akwaaba Night is an annual event organized by the department to welcome new students and introduce them to the department, faculty, and student clubs.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789776611/IMG_9253.jpg'
  },
];

export const NEWS: NewsItem[] = [
  {
    id: '1',
    title: 'Lectures Begin For Continuing Students Undergraduate',
    date: 'October 13, 2026',
    author: 'P.R.O',
    category: 'Academic',
    summary: 'All continuing undergraduate students are to report to their respective classes for the commencement of lectures for the 2026/2027 academic year.',
    image: 'https://images.unsplash.com/photo-1635070041078-e363dbe005cb?auto=format&fit=crop&q=80&w=800'
  },
  {
    id: '2',
    title: 'Late registration for Continuing Students With Fine',
    date: 'October 15, 2026 to October 23, 2026',
    author: 'P.R.O',
    category: 'academic',
    summary: 'Continuing students who have not registered for courses are to report to the department office for late registration with a fine.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790044031/Where_to_Register_My_Business_Name__A_Step-by-Step_Guide_anqium.jpg'
  },
  {
    id: '3',
    title: 'Registration For Special Resit Exams For Continuing Students',
    date: 'November 10, 2026 to November 23, 2026',
    author: 'Academic Office',
    category: 'Academic',
    summary: 'Continuing students who wish to register for special resit exams are to report to the department office for registration.',
    image: 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&q=80&w=800'
  },
  {
    id: '4',
    title: 'Lectures Begin For Freshers',
    date: 'October 19',
    author: 'P.R.O',
    category: 'academic',
    summary: 'All freshers are to report to their respective classes for the commencement of lectures for the 2026/2027 academic year.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790044031/download_54_tetw6j.jpg'
  },
  {
    id: '5',
    title: 'Matriculation Ceremony For Freshers',
    date: 'November 14, 2026',
    author: 'P.R.O',
    category: 'academic',
    summary: 'All freshers are to report to the matriculation ceremony for the commencement of lectures for the 2026/2027 academic year.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790044031/4609575054708411008_tvqqku.jpg'
  }
];

export const CLUBS: ClubItem[] = [
  {
    id: '1',
    name: 'AENICS Robotics Club',
    lead: 'Samuel Sarfo',
    description: 'Designing, building, and programming autonomous robots and IoT devices for international robotics challenges.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789762018/download_56.jpg',
    tags: ['Robotics', 'Hardware', 'ROS', 'C++'],
    iconName: 'Bot'
  },
  {
    id: '2',
    name: 'AMALITEK CODING CLUB',
    lead: 'Priya Sharma',
    description: 'Exploring machine learning, deep neural networks, computer vision, and ethical AI through collaborative projects.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789762018/React_Developer.jpg',
    tags: ['AI', 'Python', 'PyTorch', 'Data Science'],
    iconName: 'Brain'
  },
  {
    id: '3',
    name: 'Cyber Security Club',
    lead: 'David Miller',
    description: 'Focused on ethical hacking, Capture The Flag (CTF) competitions, network auditing, and secure coding practices.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789762018/_Prepare_Your_Business_for_the_Biggest_Botnet__Essential_Cybersecurity_Insights_Measures_.jpg',
    tags: ['Security', 'CTF', 'Networking', 'Linux'],
    iconName: 'ShieldCheck'
  },
    {
    id: '4',
    name: 'Google Developer Club',
    lead: 'Elena Rostova',
    description: 'Promoting contributions to open-source software, hosting Git workshops, and facilitating group software projects.',
    image:'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789766275/download_57.jpg',
    tags: ['Git', 'Full-Stack', 'Open Source', 'TypeScript'],
    iconName: 'Code'
  },
  {
    id: '5',
    name: 'BuzzChat Club',
    lead: 'Elena Rostova',
    description: 'Promoting contributions to open-source software, hosting Git workshops, and facilitating group software projects.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789766779/AI-Powered_Virtual_Support.jpg',
    tags: ['Git', 'Full-Stack', 'Open Source', 'TypeScript'],
    iconName: 'Code'
  },
    {
    id: '6',
    name: 'Code Night Club',
    lead: 'Elena Rostova',
    description: 'Promoting contributions to open-source software, hosting Git workshops, and facilitating group software projects.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789762018/download_55.jpg',
    tags: ['Git', 'Full-Stack', 'Open Source', 'TypeScript'],
    iconName: 'Code'
  }
];

export const RESOURCES: ResourceItem[] = [
  {
    id: '1',
    title: 'B.Tech CSE Full Curriculum & Syllabus (2026 Edition)',
    category: 'Syllabus',
    fileSize: '2.4 MB',
    format: 'PDF',
    description: 'Complete course structures, credits, pre-requisites, and course outcomes for all semesters.',
    link: '#'
  },
  {
    id: '2',
    title: 'Data Structures & Algorithms Lab Handbook',
    category: 'Lab Guides',
    fileSize: '4.1 MB',
    format: 'PDF',
    description: 'Lab problems, expected complexity bounds, and template code in C++ and Java.',
    link: '#'
  },
  {
    id: '3',
    title: 'Departmental Cloud VM Setup Guide & IDE Configs',
    category: 'Software',
    fileSize: '850 KB',
    format: 'PDF',
    description: 'Instructions for setting up remote Linux development environments provided by the department.',
    link: '#'
  },
  {
    id: '4',
    title: 'Undergraduate Research Thesis Guidelines',
    category: 'Research',
    fileSize: '1.2 MB',
    format: 'PDF',
    description: 'Formatting requirements, citation rules, submission deadlines, and evaluation rubrics.',
    link: '#'
  }
];

export const EXECUTIVES: Executive[] = [
  {
    id: '1',
    name: 'Dr. Robert Vance',
    role: 'Head of Department',
    bio: 'Pioneer in distributed systems and parallel computing with over 20 years of research leadership.',
    image: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&q=80&w=600',
    email: 'r.vance@cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '2',
    name: 'Dr. Elena Rostova',
    role: 'President',
    bio: 'Specialist in Computer Vision and Human-Computer Interaction, guiding curriculum evolution.',
    image: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=600',
    email: 'e.rostova@cse.edu',
    linkedin: 'https://linkedin.com'
  },
  {
    id: '3',
    name: 'Prof. Marcus Chen',
    role: 'Vice President',
    bio: 'Focuses on Embedded Hardware, IoT security, and next-generation autonomous robotics.',
    image: 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&q=80&w=600',
    email: 'm.chen@cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '4',
    name: 'Sarah Patel',
    role: 'Financial Secretary',
    bio: 'Senior student advocating for student resources, hackathons, and industry mentorship programs.',
    image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=600',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '5',
    name: 'Sarah Patel',
    role: 'Head Of Entertainment & Events',
    bio: 'Senior student advocating for student resources, hackathons, and industry mentorship programs.',
    image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=600',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '6',
    name: 'Sarah Patel',
    role: 'Public Relations Officer',
    bio: 'Senior student advocating for student resources, hackathons, and industry mentorship programs.',
    image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=600',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '7',
    name: 'Sarah Patel',
    role: 'Womens Commissioner',
    bio: 'Senior student advocating for student resources, hackathons, and industry mentorship programs.',
    image: 'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&q=80&w=600',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  }
];
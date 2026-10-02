import { NewsItem, ClubItem, ResourceItem, Executive } from '../types';

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
    iconName: 'Bot',
    joinLink: 'https://chat.whatsapp.com/EEciDXwTKStG0fqTlDgEkX?s=cl&p=i&mlu=4&ilr=4'
  },
  {
    id: '2',
    name: 'AMALITEK CODING CLUB',
    lead: 'Priya Sharma',
    description: 'Exploring machine learning, deep neural networks, computer vision, and ethical AI through collaborative projects.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789762018/React_Developer.jpg',
    tags: ['AI', 'Python', 'PyTorch', 'Data Science'],
    iconName: 'Brain',
    joinLink: 'https://whatsapp.com/channel/0029Vb854xuEKyZISP6FoL2i'
  },
  {
    id: '3',
    name: 'Cyber Security Club',
    lead: 'David Miller',
    description: 'Focused on ethical hacking, Capture The Flag (CTF) competitions, network auditing, and secure coding practices.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789762018/_Prepare_Your_Business_for_the_Biggest_Botnet__Essential_Cybersecurity_Insights_Measures_.jpg',
    tags: ['Security', 'CTF', 'Networking', 'Linux'],
    iconName: 'ShieldCheck',
    joinLink: 'https://whatsapp.com/channel/0029VadekP28fewhhN4qPP46'
  },
    {
    id: '4',
    name: 'Google Developer Club',
    lead: 'Elena Rostova',
    description: 'Promoting contributions to open-source software, hosting Git workshops, and facilitating group software projects.',
    image:'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789766275/download_57.jpg',
    tags: ['Git', 'Full-Stack', 'Open Source', 'TypeScript'],
    iconName: 'Code',
    joinLink: 'https://chat.whatsapp.com/FDW7rJjS8EtIZ7KzHm9U10?s=cl&p=a&mlu=4&ilr=4'
  },
  {
    id: '5',
    name: 'BuzzChat Club',
    lead: 'Elena Rostova',
    description: 'Promoting contributions to open-source software, hosting Git workshops, and facilitating group software projects.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789766779/AI-Powered_Virtual_Support.jpg',
    tags: ['Git', 'Full-Stack', 'Open Source', 'TypeScript'],
    iconName: 'Code',
    joinLink: '#'
  },
    {
    id: '6',
    name: 'Code Night Club',
    lead: 'Elena Rostova',
    description: 'Promoting contributions to open-source software, hosting Git workshops, and facilitating group software projects.',
    image: 'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789762018/download_55.jpg',
    tags: ['Git', 'Full-Stack', 'Open Source', 'TypeScript'],
    iconName: 'Code',
    joinLink: '#'
  }
];

export const RESOURCES: ResourceItem[] = [
  {
    id: '1',
    title: 'Applied Electricity notes and Syllabus',
    category: 'Syllabus',
    fileSize: '2.4 MB',
    format: 'PDF',
    description: 'Complete course structures, credits, pre-requisites, and course outcomes for all semesters.',
    link: 'https://drive.google.com/file/d/1g0k5J6Z7Z7Z7Z7Z7Z7Z7Z7Z7Z7Z7Z/view?usp=sharing'
  },
  {
    id: '2',
    title: 'VMware Workstation Pro Lab Guide',
    category: 'Lab Guides',
    fileSize: '4.1 MB',
    format: 'PDF',
    description: 'Lab problems, expected complexity bounds, and template code in C++ and Java.',
    link: 'https://www.vmware.com/products/workstation-pro.html'
  },
  {
    id: '3',
    title: 'Visual Studio Code Download & Setup Guide',
    category: 'Software',
    fileSize: '850 KB',
    format: 'PDF',
    description: 'Download vs code, installation steps, and recommended extensions for C++, Python, and Java development.',
    link: 'https://code.visualstudio.com/download?_exp_download=d53503e735'
  },
  {
    id: '4',
    title: 'IEEE Research Papers Formatting & Submission Guide',
    category: 'Research',
    fileSize: '1.2 MB',
    format: 'PDF',
    description: 'Formatting requirements, citation rules, submission deadlines, and evaluation rubrics.',
    link: 'https://www.ieee.org/conferences/publishing/templates.html'
  },
  {
    id: '5',
    title: 'Pycharm IDE Download & Setup Guide',
    category: 'Software',
    fileSize: '850 KB',
    format: 'PDF',
    description: 'Download PyCharm, installation steps, and recommended extensions for Python development.',
    link: 'https://www.jetbrains.com/pycharm/download/'
  },
  {
    id: '6',
    title: 'Python Programming Language Download & Setup Guide',
    category: 'Software',
    fileSize: '850 KB',
    format: 'PDF',
    description: 'Download Python, installation steps, and recommended extensions for Python development.',
    link: 'https://www.python.org/downloads/'
  },
  {
    id: '7',
    title: 'Autocad Software Download & Setup Guide',
    category: 'Software',
    fileSize: '850 KB',
    format: 'PDF',
    description: 'Download Autocad, installation steps, and recommended extensions for architectural and engineering design.',
    link: 'https://www.autodesk.com/products/autocad/free-trial'
  },
  {
    id: '8',
    title: 'Anylogic Software Download & Setup Guide',
    category: 'Software',
    fileSize: '850 KB',
    format: 'PDF',
    description: 'Download Anylogic, installation steps, and recommended extensions for simulation and modeling.',
    link: 'https://www.anylogic.com/download/'
  },
  {
    id: '9',
    title: 'VMware Workstation Pro Download & Setup Guide',
    category: 'Software',
    fileSize: '850 KB',
    format: 'PDF',
    description: 'Download VMware Workstation Pro, installation steps, and recommended extensions for virtualization.',
    link: 'https://www.vmware.com/products/workstation-pro.html'
  },
  {
    id: '10',
    title: 'VirtualBox Download & Setup Guide',
    category: 'Software',
    fileSize: '850 KB',
    format: 'PDF',
    description: 'Download VirtualBox, installation steps, and recommended extensions for virtualization.',
    link: 'https://www.virtualbox.org/wiki/Downloads'
  },
];

export const EXECUTIVES: Executive[] = [
  {
    id: '1',
    name: 'Dr. Felix Larbi Aryeh',
    role: 'Head of Department CI',
    bio: 'Specialist in Cyber Security, Information Security, Artificial Intelligence, Web Applications, Computer Graphics and many others leading the department towards cutting-edge research and innovation.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790979203/flaryeh2104373816_jkslks.jpg',
    email: 'flaryeh@umat.edu.gh',
    linkedin: 'https://www.linkedin.com/in/vincent-m-nofong-phd/',
  },
  {
    id: '2',
    name: 'Dr. Emmanuel Effah',
    role: 'Head of Department CE',
    bio: 'Specialist in Embedded Systems, IoT, and Cyber-Physical Systems, leading the department towards cutting-edge research and innovation.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790979311/IMG_0267_2_lxugl0.jpg',
    email: 'eeffah@umat.edu.gh',
    linkedin: 'https://www.linkedin.com/in/vincent-m-nofong-phd/',
  },
  {
    id: '3',
    name: 'Mr. Derick Duku',
    role: 'President',
    bio: 'A year 4 computer science student with a passion for software development, AI, and community engagement, leading the department\'s student association.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790047333/IMG_9094_c7shxu.jpg',
    email: 'e.rostova@cse.edu',
    linkedin: 'https://www.linkedin.com/in/derrick-duku-6ab823353/'
  },
  {
    id: '4',
    name: 'Mr. Nuworkpor C. Selorm K.',
    role: 'Vice President',
    bio: 'Focuses on Embedded Hardware, IoT security, and next-generation autonomous robotics.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981286/Business_Man_Suit_Silhouette_-_Free_vector_graphic_on_Pixabay_tpdwz0.jpg',
    email: 'm.chen@cse.edu',
    linkedin: 'https://linkedin.com',
  },
  {
    id: '5',
    name: 'Gloria Afia Brenya',
    role: 'General Secretary',
    bio: 'Dedicated to promoting student welfare and organizing departmental events.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981500/Businesswoman_free_icons_designed_by_Eucalyp_1_ueptey.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '6',
    name: 'Amaama Yvonne',
    role: 'Deputy General Secretary',
    bio: 'Senior student advocating for student resources, hackathons, and industry mentorship programs.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981500/Businesswoman_free_icons_designed_by_Eucalyp_1_ueptey.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '7',
    name: 'Christian Edeamu Cudjoe',
    role: 'Public Relations Officer',
    bio: 'Student with a passion for technology and communication, responsible for managing the department\'s public image and media relations.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981286/Business_Man_Suit_Silhouette_-_Free_vector_graphic_on_Pixabay_tpdwz0.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '8',
    name: 'Kupualor Endurance Korblah',
    role: 'Organizing Secretary',
    bio: 'Student focused on event planning and coordination, ensuring smooth execution of departmental activities and initiatives.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981286/Business_Man_Suit_Silhouette_-_Free_vector_graphic_on_Pixabay_tpdwz0.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '9',
    name: 'Samuel Gyamfi',
    role: 'Financial Secretary',
    bio: 'Student with a keen interest in finance and budgeting, responsible for managing the department\'s financial resources and ensuring transparency in financial matters.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981286/Business_Man_Suit_Silhouette_-_Free_vector_graphic_on_Pixabay_tpdwz0.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '10',
    name: 'Fitzgerald Kwesi Josiah Kwofie',
    role: 'Project Manager',
    bio: 'Student with a strong background in project management and software development, overseeing departmental projects and ensuring timely delivery of initiatives.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981286/Business_Man_Suit_Silhouette_-_Free_vector_graphic_on_Pixabay_tpdwz0.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '11',
    name: 'Osman Sumaila Bipembi',
    role: 'Entertainment Chairman',
    bio: 'Student with a passion for entertainment and event management, responsible for organizing social events and activities within the department.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981500/Businesswoman_free_icons_designed_by_Eucalyp_1_ueptey.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '12',
    name: 'Boampong Boakye Nyameadom Jude',
    role: 'Sports Chairman',
    bio: 'Student with a strong interest in sports and physical activities, responsible for promoting sportsmanship and organizing sports events within the department.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981286/Business_Man_Suit_Silhouette_-_Free_vector_graphic_on_Pixabay_tpdwz0.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '13',
    name: 'Serwaa Nyira Johnson',
    role: 'Deputy Sports Chairperson',
    bio: 'Student with a keen interest in sports and physical activities, assisting the Sports Chairman in promoting sportsmanship and organizing sports events within the department.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981500/Businesswoman_free_icons_designed_by_Eucalyp_1_ueptey.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  },
  {
    id: '14',
    name: 'Julier Appiah-Kubi',
    role: 'Womens Commissioner',
    bio: 'Student focused on advocating for women\'s rights and gender equality within the department, organizing events and initiatives to support female students.',
    image: 'https://res.cloudinary.com/dzydzt8x8/image/upload/v1790981500/Businesswoman_free_icons_designed_by_Eucalyp_1_ueptey.jpg',
    email: 's.patel@student.cse.edu',
    linkedin: 'https://linkedin.com',
    github: 'https://github.com'
  }
  
];
import React from 'react';
import { LegalPage } from './LegalPage';

const sections = [
  {
    heading: 'Information We Collect',
    paragraphs: [
      'We collect information that helps us deliver a better experience for students, guests, and administrators using the ACSES-UMaT website. This may include basic contact details submitted through forms, technical information such as browser type and device details, and usage data that helps us understand how visitors navigate the site.',
      'If you register for an event, newsletter, or club communication, we may keep your name, email address, and other relevant information needed to serve you effectively.'
    ]
  },
  {
    heading: 'How We Use Your Information',
    paragraphs: [
      'We use the information we collect to manage admissions inquiries, event participation, club engagement, academic resources, and communication with the student community. We may also use your information to improve site functionality, respond to support requests, and maintain effective contact with members of the ACSES association.',
      'We do not sell, rent, or trade personal information to third parties for marketing purposes.'
    ]
  },
  {
    heading: 'Cookies and Analytics',
    paragraphs: [
      'The website may use cookies or similar technologies to remember user preferences, maintain session activity, and understand usage trends. This helps us improve website performance, navigation, and the quality of our resources.',
      'Analytics data may be used in aggregate form to monitor which pages are most useful and to make the website more accessible and informative.'
    ]
  },
  {
    heading: 'Third-Party Links',
    paragraphs: [
      'Our website may include links to external resources such as UMaT systems, Google services, research pages, and learning platforms. These external sites are governed by their own privacy policies, and we recommend reviewing them before sharing personal information.'
    ]
  },
  {
    heading: 'Security',
    paragraphs: [
      'We take reasonable measures to protect information submitted through our website. However, no online system can guarantee complete security, and we encourage users to avoid submitting sensitive personal information through public channels unless expressly required.'
    ]
  },
  {
    heading: 'Policy Updates',
    paragraphs: [
      'This Privacy Policy may be updated from time to time to reflect improvements in site operations, legal requirements, or departmental communication practices. Changes will take effect when published on this page.'
    ]
  }
];

export const PrivacyPolicyPage: React.FC = () => (
  <LegalPage
    title="Privacy Policy"
    intro="This Privacy Policy explains how ACSES-UMaT collects, uses, and protects personal information in connection with the operation of this website."
    sections={sections}
  />
);

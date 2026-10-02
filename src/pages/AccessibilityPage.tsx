import React from 'react';
import { LegalPage } from './LegalPage';

const sections = [
  {
    heading: 'Our Commitment',
    paragraphs: [
      'ACSES-UMaT is committed to making this website accessible to all users, including people with disabilities. We aim to provide an inclusive digital experience that supports navigation, readability, and understanding for diverse users.'
    ]
  },
  {
    heading: 'Accessibility Standards',
    paragraphs: [
      'We work to align the website with widely accepted accessibility standards and best practices, including clear structure, readable text, meaningful link labels, contrast considerations, and consistent navigation patterns.',
      'We recognize that accessibility is an ongoing effort and we continue to review and improve the digital experience as technology and user needs evolve.'
    ]
  },
  {
    heading: 'Known Limitations',
    paragraphs: [
      'Some third-party tools, embedded content, or external systems linked from the website may not yet meet the same accessibility standards. Where possible, we provide alternative access routes or clearly identify such content.'
    ]
  },
  {
    heading: 'Feedback',
    paragraphs: [
      'If you experience any accessibility barriers while using this website, please contact the ACSES team so we can investigate the issue and improve the experience. We welcome feedback and suggestions on how we can make the site easier to use.'
    ]
  },
  {
    heading: 'Continuous Improvement',
    paragraphs: [
      'Accessibility is reviewed regularly as part of our ongoing website maintenance. We aim to improve usability, navigation, and readability for all users over time.'
    ]
  }
];

export const AccessibilityPage: React.FC = () => (
  <LegalPage
    title="Accessibility Policy"
    intro="This Accessibility Policy explains our commitment to providing a website that is usable, understandable, and navigable for people with disabilities."
    sections={sections}
  />
);

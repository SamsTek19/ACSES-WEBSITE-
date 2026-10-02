import React from 'react';
import { LegalPage } from './LegalPage';

const sections = [
  {
    heading: 'Acceptance of Terms',
    paragraphs: [
      'By accessing or using the ACSES-UMaT website, you agree to comply with these Terms of Service and all applicable departmental, institutional, and legal requirements. If you do not agree with any part of these terms, you should not use the website.'
    ]
  },
  {
    heading: 'Website Use',
    paragraphs: [
      'This website is intended for informational and academic communication purposes. Users may access news, resources, events, club information, and departmental updates. You agree to use the site lawfully and not interfere with the operation, security, or accessibility of the website.',
      'Any misuse of the website, including unauthorized access, abusive behavior, spam submissions, or malicious activity, may result in restricted access or disciplinary action where applicable.'
    ]
  },
  {
    heading: 'Content and Accuracy',
    paragraphs: [
      'The Association of Computer Science & Engineering Students strives to provide current and relevant information. However, content may change without notice. We do not guarantee that all information is complete, error-free, or always available at all times.',
      'Links to external websites are provided for convenience and informational purposes. We are not responsible for the content or reliability of third-party websites.'
    ]
  },
  {
    heading: 'Intellectual Property',
    paragraphs: [
      'All text, graphics, logos, images, and website content are the property of ACSES-UMaT or their respective owners unless otherwise stated. Unauthorized use, reproduction, or distribution of website content may violate applicable laws and policies.'
    ]
  },
  {
    heading: 'Liability',
    paragraphs: [
      'ACSES-UMaT makes every effort to keep the website available and accurate, but we do not accept liability for loss, damage, or interruption arising from use of the site. This includes but is not limited to downtime, link failures, inaccurate content, or third-party service issues.'
    ]
  },
  {
    heading: 'Changes to Terms',
    paragraphs: [
      'We reserve the right to update or amend these Terms of Service at any time. Continued use of the website after changes are posted constitutes acceptance of the revised terms.'
    ]
  }
];

export const TermsPage: React.FC = () => (
  <LegalPage
    title="Terms of Service"
    intro="These Terms of Service outline the rules and responsibilities for using the ACSES-UMaT website and its related information resources."
    sections={sections}
  />
);

with restored_events (
    title, description, location, start_at, end_at, time_label, category,
    cta_url, memories_link, banner_path
) as (
    values
        (
            'Biometric Registration For Continuing Students',
            'Students are to register for courses for the upcoming semester.',
            'ACSES Department Office',
            '2026-10-12 10:00:00+00'::timestamptz,
            '2026-10-14 16:00:00+00'::timestamptz,
            null::text,
            'Registration',
            null::text,
            null::text,
            'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774764/IMG_9040.jpg'
        ),
        (
            'Freshers Orientation & Medical Examination Of Freshers',
            'An orientation program for new students, and all first year students are to be introduced to the department, faculty, and student clubs.',
            'UMaT Main Auditorium',
            '2026-10-12 09:00:00+00'::timestamptz,
            '2026-10-23 17:00:00+00'::timestamptz,
            null::text,
            'Orientation',
            null::text,
            null::text,
            'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&q=80&w=800'
        ),
        (
            'Annual Hackathon 2026: AI for Good',
            'A hackathon bringing together students to solve real-world problems using artificial intelligence and robotics.',
            'Main Auditorium',
            null::timestamptz,
            null::timestamptz,
            '09:00 AM - 06:00 PM',
            'Hackathon',
            null::text,
            null::text,
            'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789774758/IMG_9404.jpg'
        ),
        (
            'Innovation & Research Symposium 2026',
            'Experts, researchers, and students present research findings, innovative projects, and technological advancements in a symposium format.',
            'To be announced',
            null::timestamptz,
            null::timestamptz,
            null::text,
            'Symposium',
            null::text,
            null::text,
            'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&q=80&w=800'
        ),
        (
            'Annual Hackathon 2025: AI for Good',
            'Last year''s hackathon brought together students to solve real-world problems using artificial intelligence and robotics.',
            'Main Auditorium',
            '2025-10-15 09:00:00+00'::timestamptz,
            '2025-10-15 18:00:00+00'::timestamptz,
            null::text,
            'Hackathon',
            null::text,
            'https://drive.google.com/drive/u/0/mobile/folders/18-VTlVSNvyiaA4OnHj6w3jaH8iE8oHU0?usp=drive_link',
            'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789777230/IMG_9081.jpg'
        ),
        (
            'ACSES Annual Dinner and Awards Night',
            'Last year''s annual dinner and awards night celebrated the achievements of students, faculty, and staff in the department.',
            'Main Auditorium',
            '2026-10-15 09:00:00+00'::timestamptz,
            '2026-10-15 18:00:00+00'::timestamptz,
            null::text,
            'Social',
            null::text,
            'https://bigmethphotography01.pixieset.com/acsesredcarpet/',
            'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789777046/IMG_9123.jpg'
        ),
        (
            'Kakalika Freshers Akwaaba Night',
            'Freshers Akwaaba Night is an annual event organized by the department to welcome new students and introduce them to the department, faculty, and student clubs.',
            'Main Auditorium',
            '2026-10-15 09:00:00+00'::timestamptz,
            '2026-10-15 18:00:00+00'::timestamptz,
            null::text,
            'Social',
            null::text,
            'https://bigmethphotography01.pixieset.com/kakalikafreshersakwaaba/',
            'https://res.cloudinary.com/ndm0k2b2/image/upload/v1789776611/IMG_9253.jpg'
        )
)
insert into public.portal_events (
    title, description, location, start_at, end_at, time_label, category,
    cta_url, memories_link, banner_path
)
select
    source.title, source.description, source.location, source.start_at, source.end_at,
    source.time_label, source.category, source.cta_url, source.memories_link, source.banner_path
from restored_events source
where not exists (
    select 1 from public.portal_events existing where existing.title = source.title
);

create extension if not exists pgcrypto with schema extensions;

create table public.portal_profiles (
    id uuid primary key references auth.users (id) on delete cascade,
    email text not null unique,
    username text not null unique,
    full_name text not null,
    phone_number text,
    index_number text unique,
    class_name text check (class_name is null or class_name in ('Cyber Security', 'Computer Science', 'Information System')),
    year smallint check (year is null or year between 1 and 4),
    department text,
    role text not null default 'student' check (role in ('student', 'admin', 'faculty')),
    account_status text not null default 'pending' check (account_status in ('pending', 'approved', 'rejected', 'suspended')),
    rejection_reason text,
    reviewed_by uuid references public.portal_profiles (id) on delete set null,
    reviewed_at timestamptz,
    is_graduated boolean not null default false,
    profile_picture text,
    latitude numeric(10, 8),
    longitude numeric(11, 8),
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    check (char_length(username) between 1 and 50),
    check (char_length(full_name) between 1 and 150)
);

create unique index portal_profiles_username_lower_unique on public.portal_profiles (lower(username));
create unique index portal_profiles_index_number_lower_unique on public.portal_profiles (lower(index_number)) where index_number is not null;

create table public.portal_events (
    id uuid primary key default gen_random_uuid(),
    title text not null,
    location text,
    description text,
    start_at timestamptz,
    end_at timestamptz,
    time_label text,
    category text,
    cta_url text,
    memories_link text,
    banner_path text,
    banner_alt text,
    created_by uuid references public.portal_profiles (id) on delete set null,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    check (end_at is null or start_at is null or end_at >= start_at)
);
create index portal_events_start_at_index on public.portal_events (start_at);

create table public.portal_announcements (
    id uuid primary key default gen_random_uuid(),
    title text not null,
    slug text not null unique,
    excerpt text,
    content text,
    type text not null default 'general' check (type in ('general', 'security', 'maintenance')),
    priority text not null default 'normal' check (priority in ('low', 'normal', 'high')),
    target_type text not null default 'all' check (target_type in ('all', 'student', 'class', 'year', 'class_year')),
    target_profile_ids uuid[] not null default '{}',
    target_classes text[] not null default '{}',
    target_years smallint[] not null default '{}',
    published_at timestamptz,
    author_id uuid references public.portal_profiles (id) on delete set null,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);
create index portal_announcements_published_at_index on public.portal_announcements (published_at);

create table public.portal_support_resources (
    id uuid primary key default gen_random_uuid(),
    title text not null,
    category text not null default 'support',
    resource_type text not null default 'link' check (resource_type in ('link', 'file', 'external')),
    content_type text not null default 'general',
    cta_label text,
    cta_url text,
    file_path text,
    visibility text not null default 'student' check (visibility in ('student', 'public')),
    description text,
    icon text,
    target_classes text[] not null default '{}',
    target_years smallint[] not null default '{}',
    display_order integer not null default 0,
    created_by uuid references public.portal_profiles (id) on delete set null,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);
create index portal_support_resources_display_order_index on public.portal_support_resources (display_order, title);

create table public.portal_academic_timeline_entries (
    id uuid primary key default gen_random_uuid(),
    title text not null,
    summary text,
    description text,
    starts_at timestamptz not null,
    academic_year text,
    cta_label text,
    cta_url text,
    is_published boolean not null default true,
    created_by uuid references public.portal_profiles (id) on delete set null,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);
create index portal_academic_timeline_entries_starts_at_index on public.portal_academic_timeline_entries (starts_at);

create table public.portal_suggestions (
    id uuid primary key default gen_random_uuid(),
    user_id uuid references public.portal_profiles (id) on delete set null,
    sender_name text,
    sender_email text,
    category text not null,
    subject text not null check (char_length(subject) <= 160),
    message text not null,
    attachment_path text,
    status text not null default 'pending' check (status in ('pending', 'in_progress', 'resolved', 'rejected')),
    handled_at timestamptz,
    handled_by uuid references public.portal_profiles (id) on delete set null,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);
create index portal_suggestions_status_created_at_index on public.portal_suggestions (status, created_at desc);

create table public.portal_elections (
    id uuid primary key default gen_random_uuid(),
    title text not null,
    description text,
    start_at timestamptz not null,
    end_at timestamptz not null,
    status text not null default 'draft' check (status in ('draft', 'active', 'completed', 'cancelled')),
    created_by uuid references public.portal_profiles (id) on delete set null,
    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),
    check (end_at > start_at)
);

create table public.portal_election_positions (
    id uuid primary key default gen_random_uuid(),
    election_id uuid not null references public.portal_elections (id) on delete cascade,
    title text not null,
    description text,
    display_order integer not null default 0,
    created_at timestamptz not null default now(),
    unique (election_id, title),
    unique (id, election_id)
);

create table public.portal_election_candidates (
    id uuid primary key default gen_random_uuid(),
    election_id uuid not null references public.portal_elections (id) on delete cascade,
    position_id uuid not null,
    name text not null,
    photo_url text,
    bio text,
    short_description text,
    created_at timestamptz not null default now(),
    unique (id, election_id),
    foreign key (position_id, election_id) references public.portal_election_positions (id, election_id) on delete cascade
);
create index portal_election_candidates_position_index on public.portal_election_candidates (position_id);

create table public.portal_election_voters (
    election_id uuid not null references public.portal_elections (id) on delete cascade,
    profile_id uuid not null references public.portal_profiles (id) on delete cascade,
    is_eligible boolean not null default true,
    has_voted boolean not null default false,
    invitation_sent_at timestamptz,
    voted_at timestamptz,
    primary key (election_id, profile_id)
);

create table public.portal_election_ballots (
    id uuid primary key default gen_random_uuid(),
    election_id uuid not null references public.portal_elections (id) on delete cascade,
    candidate_id uuid not null,
    cast_at timestamptz not null default now(),
    foreign key (candidate_id, election_id) references public.portal_election_candidates (id, election_id) on delete cascade
);
create index portal_election_ballots_election_candidate_index on public.portal_election_ballots (election_id, candidate_id);

create table public.portal_election_audit_logs (
    id bigint generated always as identity primary key,
    actor_id uuid references public.portal_profiles (id) on delete set null,
    election_id uuid references public.portal_elections (id) on delete set null,
    action text not null,
    details jsonb not null default '{}',
    created_at timestamptz not null default now()
);
create index portal_election_audit_logs_election_created_index on public.portal_election_audit_logs (election_id, created_at desc);

create or replace function public.set_updated_at()
returns trigger
language plpgsql
set search_path = ''
as $$
begin
    new.updated_at := now();
    return new;
end;
$$;

create or replace function public.is_portal_admin()
returns boolean
language sql
stable
security definer
set search_path = ''
as $$
    select exists (
        select 1 from public.portal_profiles p
        where p.id = (select auth.uid())
          and p.role = 'admin'
          and p.account_status = 'approved'
    );
$$;

create or replace function public.is_approved_student()
returns boolean
language sql
stable
security definer
set search_path = ''
as $$
    select exists (
        select 1 from public.portal_profiles p
        where p.id = (select auth.uid())
          and p.role = 'student'
          and p.account_status = 'approved'
    );
$$;

create or replace function public.handle_new_auth_user()
returns trigger
language plpgsql
security definer
set search_path = ''
as $$
declare
    generated_username text;
begin
    generated_username := coalesce(
        nullif(new.raw_user_meta_data ->> 'username', ''),
        split_part(coalesce(new.email, new.id::text), '@', 1) || '_' || left(new.id::text, 8)
    );

    insert into public.portal_profiles (id, email, username, full_name, phone_number, index_number, class_name, year)
    values (
        new.id,
        coalesce(new.email, ''),
        generated_username,
        coalesce(nullif(new.raw_user_meta_data ->> 'full_name', ''), split_part(coalesce(new.email, 'Student'), '@', 1)),
        nullif(new.raw_user_meta_data ->> 'phone_number', ''),
        nullif(new.raw_user_meta_data ->> 'index_number', ''),
        case
            when new.raw_user_meta_data ->> 'class_name' in ('Cyber Security', 'Computer Science', 'Information System')
                then new.raw_user_meta_data ->> 'class_name'
            else null
        end,
        case
            when new.raw_user_meta_data ->> 'year' in ('1', '2', '3', '4')
                then (new.raw_user_meta_data ->> 'year')::smallint
            else null
        end
    )
    on conflict (id) do nothing;

    return new;
end;
$$;

create trigger on_auth_user_created
    after insert on auth.users
    for each row execute function public.handle_new_auth_user();

create or replace function public.cast_election_vote(p_election_id uuid, p_candidate_id uuid)
returns void
language plpgsql
security definer
set search_path = ''
as $$
declare
    voter_id uuid := (select auth.uid());
begin
    if voter_id is null or not public.is_approved_student() then
        raise exception 'An approved student account is required to vote.' using errcode = '42501';
    end if;

    if not exists (
        select 1 from public.portal_elections e
        where e.id = p_election_id
          and e.status = 'active'
          and e.start_at <= now()
          and e.end_at > now()
    ) then
        raise exception 'This election is not open for voting.' using errcode = '22023';
    end if;

    if not exists (
        select 1 from public.portal_election_candidates c
        where c.id = p_candidate_id and c.election_id = p_election_id
    ) then
        raise exception 'The selected candidate is not in this election.' using errcode = '22023';
    end if;

    update public.portal_election_voters
       set has_voted = true, voted_at = now()
     where election_id = p_election_id
       and profile_id = voter_id
       and is_eligible = true
       and has_voted = false;

    if not found then
        raise exception 'You are not eligible to vote or have already voted.' using errcode = '23505';
    end if;

    insert into public.portal_election_ballots (election_id, candidate_id)
    values (p_election_id, p_candidate_id);

    insert into public.portal_election_audit_logs (actor_id, election_id, action)
    values (voter_id, p_election_id, 'vote_cast');
end;
$$;

create or replace function public.get_election_results(p_election_id uuid)
returns table (
    position_id uuid,
    position_title text,
    candidate_id uuid,
    candidate_name text,
    vote_count bigint
)
language plpgsql
stable
security definer
set search_path = ''
as $$
begin
    if (select auth.uid()) is null then
        raise exception 'Authentication is required.' using errcode = '42501';
    end if;

    if not public.is_portal_admin() and not public.is_approved_student() then
        raise exception 'An approved portal account is required.' using errcode = '42501';
    end if;

    if not public.is_portal_admin() and not exists (
        select 1 from public.portal_elections e
        where e.id = p_election_id and e.end_at <= now()
          and exists (
              select 1 from public.portal_election_voters v
              where v.election_id = e.id
                and v.profile_id = (select auth.uid())
                and v.is_eligible = true
          )
    ) then
        raise exception 'Results are available only to eligible voters after the election ends.' using errcode = '42501';
    end if;

    return query
    select p.id, p.title, c.id, c.name, count(b.id)::bigint
    from public.portal_election_positions p
    join public.portal_election_candidates c on c.position_id = p.id and c.election_id = p.election_id
    left join public.portal_election_ballots b on b.candidate_id = c.id and b.election_id = p.election_id
    where p.election_id = p_election_id
    group by p.id, p.title, p.display_order, c.id, c.name
    order by p.display_order, p.title, c.name;
end;
$$;

revoke all on function public.set_updated_at() from public;
revoke all on function public.is_portal_admin() from public;
revoke all on function public.is_approved_student() from public;
revoke all on function public.handle_new_auth_user() from public;
revoke all on function public.cast_election_vote(uuid, uuid) from public;
revoke all on function public.get_election_results(uuid) from public;
grant execute on function public.is_portal_admin() to authenticated;
grant execute on function public.is_approved_student() to authenticated;
grant execute on function public.cast_election_vote(uuid, uuid) to authenticated;
grant execute on function public.get_election_results(uuid) to authenticated;

grant usage on schema public to anon, authenticated;
grant select on public.portal_events to anon, authenticated;
grant select, insert, update, delete on public.portal_profiles, public.portal_events,
    public.portal_announcements, public.portal_support_resources, public.portal_academic_timeline_entries,
    public.portal_suggestions, public.portal_elections, public.portal_election_positions,
    public.portal_election_candidates, public.portal_election_voters to authenticated;
grant select on public.portal_election_audit_logs to authenticated;

do $$
declare
    table_name text;
begin
    foreach table_name in array array[
        'portal_profiles', 'portal_events', 'portal_announcements', 'portal_support_resources', 'portal_academic_timeline_entries',
        'portal_suggestions', 'portal_elections', 'portal_election_positions', 'portal_election_candidates', 'portal_election_voters',
        'portal_election_ballots', 'portal_election_audit_logs'
    ] loop
        execute format('alter table public.%I enable row level security', table_name);
    end loop;
end;
$$;

create trigger profiles_set_updated_at before update on public.portal_profiles for each row execute function public.set_updated_at();
create trigger events_set_updated_at before update on public.portal_events for each row execute function public.set_updated_at();
create trigger announcements_set_updated_at before update on public.portal_announcements for each row execute function public.set_updated_at();
create trigger support_resources_set_updated_at before update on public.portal_support_resources for each row execute function public.set_updated_at();
create trigger academic_timeline_entries_set_updated_at before update on public.portal_academic_timeline_entries for each row execute function public.set_updated_at();
create trigger suggestions_set_updated_at before update on public.portal_suggestions for each row execute function public.set_updated_at();
create trigger elections_set_updated_at before update on public.portal_elections for each row execute function public.set_updated_at();

create policy "Profiles read own or admin" on public.portal_profiles
    for select to authenticated using (id = (select auth.uid()) or (select public.is_portal_admin()));
create policy "Admins manage profiles" on public.portal_profiles
    for all to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Events are public" on public.portal_events
    for select to anon, authenticated using (true);
create policy "Admins manage events" on public.portal_events
    for all to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Students read targeted published announcements" on public.portal_announcements
    for select to authenticated using (
        (select public.is_approved_student())
        and published_at is not null and published_at <= now()
        and (
            target_type = 'all'
            or (target_type = 'student' and (select auth.uid()) = any(target_profile_ids))
            or (target_type = 'class' and (select class_name from public.portal_profiles where id = (select auth.uid())) = any(target_classes))
            or (target_type = 'year' and (select year from public.portal_profiles where id = (select auth.uid())) = any(target_years))
            or (target_type = 'class_year'
                and (select class_name from public.portal_profiles where id = (select auth.uid())) = any(target_classes)
                and (select year from public.portal_profiles where id = (select auth.uid())) = any(target_years))
        )
    );
create policy "Admins manage announcements" on public.portal_announcements
    for all to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Students read visible resources" on public.portal_support_resources
    for select to authenticated using (
        visibility = 'public'
        or (
            (select public.is_approved_student())
            and (cardinality(target_classes) = 0 or (select class_name from public.portal_profiles where id = (select auth.uid())) = any(target_classes))
            and (cardinality(target_years) = 0 or (select year from public.portal_profiles where id = (select auth.uid())) = any(target_years))
        )
    );
create policy "Admins manage resources" on public.portal_support_resources
    for all to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Students read published timeline" on public.portal_academic_timeline_entries
    for select to authenticated using ((select public.is_approved_student()) and is_published = true);
create policy "Admins manage timeline" on public.portal_academic_timeline_entries
    for all to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Students create own suggestions" on public.portal_suggestions
    for insert to authenticated with check (
        user_id = (select auth.uid()) and (select public.is_approved_student())
    );
create policy "Users read own suggestions" on public.portal_suggestions
    for select to authenticated using (user_id = (select auth.uid()) or (select public.is_portal_admin()));
create policy "Admins update suggestions" on public.portal_suggestions
    for update to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Eligible students read elections" on public.portal_elections
    for select to authenticated using (
        (select public.is_portal_admin())
        or exists (
            select 1 from public.portal_election_voters v
            where v.election_id = portal_elections.id and v.profile_id = (select auth.uid()) and v.is_eligible
        )
    );
create policy "Admins manage elections" on public.portal_elections
    for all to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Eligible students read positions" on public.portal_election_positions
    for select to authenticated using (
        (select public.is_portal_admin())
        or exists (
            select 1 from public.portal_election_voters v
            where v.election_id = portal_election_positions.election_id and v.profile_id = (select auth.uid()) and v.is_eligible
        )
    );
create policy "Admins manage positions" on public.portal_election_positions
    for all to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Eligible students read candidates" on public.portal_election_candidates
    for select to authenticated using (
        (select public.is_portal_admin())
        or exists (
            select 1 from public.portal_election_voters v
            where v.election_id = portal_election_candidates.election_id and v.profile_id = (select auth.uid()) and v.is_eligible
        )
    );
create policy "Admins manage candidates" on public.portal_election_candidates
    for all to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Users read own voter status" on public.portal_election_voters
    for select to authenticated using (profile_id = (select auth.uid()) or (select public.is_portal_admin()));
create policy "Admins manage voter eligibility" on public.portal_election_voters
    for all to authenticated using ((select public.is_portal_admin())) with check ((select public.is_portal_admin()));

create policy "Admins read election audit logs" on public.portal_election_audit_logs
    for select to authenticated using ((select public.is_portal_admin()));

insert into storage.buckets (id, name, public, file_size_limit)
values
    ('portal-public', 'portal-public', true, 52428800),
    ('student-private', 'student-private', false, 20971520)
on conflict (id) do nothing;

create policy "Public portal assets are readable" on storage.objects
    for select to anon, authenticated using (bucket_id = 'portal-public');
create policy "Admins manage public portal assets" on storage.objects
    for all to authenticated using (bucket_id = 'portal-public' and (select public.is_portal_admin()))
    with check (bucket_id = 'portal-public' and (select public.is_portal_admin()));
create policy "Users read own private uploads" on storage.objects
    for select to authenticated using (
        bucket_id = 'student-private'
        and ((storage.foldername(name))[1] = (select auth.uid())::text or (select public.is_portal_admin()))
    );
create policy "Approved students read shared resources" on storage.objects
    for select to authenticated using (
        bucket_id = 'student-private'
        and (storage.foldername(name))[1] = 'resources'
        and (select public.is_approved_student())
    );
create policy "Users manage own private uploads" on storage.objects
    for all to authenticated using (
        bucket_id = 'student-private'
        and ((storage.foldername(name))[1] = (select auth.uid())::text or (select public.is_portal_admin()))
    ) with check (
        bucket_id = 'student-private'
        and ((storage.foldername(name))[1] = (select auth.uid())::text or (select public.is_portal_admin()))
    );

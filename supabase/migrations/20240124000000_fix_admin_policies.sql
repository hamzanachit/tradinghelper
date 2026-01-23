-- Migration: Fix Admin RLS Policies
-- Run with: supabase db push

-- Drop existing problematic policies
DROP POLICY IF EXISTS "Admins can view themselves" ON admins;
DROP POLICY IF EXISTS "Super admins manage admins" ON admins;

-- Admins Policies - simple email check
CREATE POLICY "Admins can view themselves" ON admins
  FOR SELECT USING (email = auth.jwt()->>'email');

CREATE POLICY "Super admins manage admins" ON admins
  FOR ALL USING (
    role = 'super_admin' AND email = auth.jwt()->>'email'
  );

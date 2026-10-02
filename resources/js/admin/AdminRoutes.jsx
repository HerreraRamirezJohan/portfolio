/*
| Every /admin/... route. Imported lazily from app.jsx so the admin code is
| a separate chunk that public visitors never download.
*/

import { Route, Routes } from 'react-router';
import { NotFound } from '../pages/NotFound';
import { AdminLayout } from './AdminLayout';
import { EducationManager, ExperienceManager, LanguageManager, ProjectManager, SkillGroupManager } from './managers';
import { Dashboard, Login, ProfileForm } from './pages';

export default function AdminRoutes() {
    return (
        <Routes>
            <Route element={<AdminLayout />}>
                <Route index element={<Dashboard />} />
                <Route path="login" element={<Login />} />
                <Route path="profile" element={<ProfileForm />} />
                <Route path="experience" element={<ExperienceManager />} />
                <Route path="education" element={<EducationManager />} />
                <Route path="skills" element={<SkillGroupManager />} />
                <Route path="languages" element={<LanguageManager />} />
                <Route path="projects" element={<ProjectManager />} />
                <Route path="*" element={<NotFound />} />
            </Route>
        </Routes>
    );
}

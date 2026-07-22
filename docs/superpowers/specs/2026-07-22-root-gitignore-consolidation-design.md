# Root `.gitignore` Consolidation Design

## Goal

Maintain one project-level `.gitignore` at the `lynslittlekitchen` repository root for the Nuxt frontend and Laravel backend.

## Scope

- Preserve all meaningful rules from the former `frontend/.gitignore` and the existing `backend/.gitignore`.
- Express project-specific rules with `/frontend/` and `/backend/` prefixes so they apply only to the intended application.
- Remove duplicate rules and rules whose leading slash currently points at the repository root instead of the backend.
- Delete the top-level `frontend/.gitignore` and `backend/.gitignore` after consolidation.
- Keep Laravel's nested `.gitignore` placeholder files under `backend/bootstrap`, `backend/database`, and `backend/storage`; Laravel uses them to retain required empty directories while ignoring their generated contents.

## Safety and Verification

- Keep `.env.example` files trackable while ignoring local `.env` variants.
- Ignore frontend dependencies and build output, including `node_modules`, `.nuxt`, `.output`, and `dist`.
- Ignore backend dependencies and runtime output, including `vendor`, backend `node_modules`, public build output, logs, cache files, and storage-generated files.
- Use `git check-ignore -v` against representative frontend and backend paths to confirm each rule resolves to the root `.gitignore`.
- Verify both example environment files remain tracked or unignored and confirm the working tree contains only the intended changes.

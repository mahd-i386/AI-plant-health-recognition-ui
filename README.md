# AI Plant Health Recognition - UI Microservice

A lightweight Laravel Blade UI microservice that accepts plant images, sends them to an external AI prediction API, and displays the returned diagnosis in a clean, responsive interface. This repository contains the UI layer only — plug any compatible AI backend.

## Overview

This project is a frontend microservice for plant-health detection. It accepts image uploads (drag-and-drop or file input), sends them as `multipart/form-data` to a configured AI API endpoint, and displays the results (plant, disease, severity, recommendations) in a simple, modern UI.

## Features

- Image upload (drag-and-drop and file input)
- Sends image to remote AI API
- Displays structured prediction results (plant, disease, severity, recommendation)
- Lightweight and easy to integrate
- Configurable API endpoint and API key via environment variables

## How it works

1. User uploads a plant image.
2. UI sends the image to the configured AI API (POST multipart/form-data).
3. API returns a JSON prediction containing fields such as `plant`, `disease`, `severity`, and `recommendation`.
4. UI parses and displays the prediction to the user.

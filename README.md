# Emotion-Aware Entertainment Recommendation System

## Overview
This project implements an emotion-aware recommendation system that suggests entertainment content (music and movies) based on user emotional input.

The system integrates Natural Language Processing (NLP) techniques with a rule-based recommendation engine.

## Technologies Used
- Frontend: Laravel Blade + JavaScript (Fetch API)
- Backend: Laravel (PHP)
- AI Service: FastAPI (Python)
- NLP Models:
  - Transformer-based model (DistilRoBERTa)
  - VADER sentiment analysis

## System Architecture
User Input → Laravel → FastAPI → Emotion Detection → Recommendation Engine → Response

## Features
- Real-time emotion detection from text input
- Dual-model support (Transformer + VADER)
- Emotion-to-mood mapping
- Rule-based recommendation system
- Interactive chatbot interface

## Setup Instructions

### FastAPI Backend
```bash
pip install -r requirements.txt
uvicorn main:app --reload
 ```

### Laravel
```bash
composer install
php artisan serve
```

## Project Structure

This project consists of two components:

### 1. Laravel Application (Frontend + API Gateway)
Located in this repository.

### 2. FastAPI Backend (Emotion Detection Service)
Available at:
https://github.com/Menchie22/emotion-based-entertainment-recommender

## Key Files

### FastAPI
- detector.py → Selects emotion detection model
- recommend.py → Generates recommendations
- rules.py → Emotion-to-mood mapping

### Laravel
- ChatbotController.php → Handles API requests
- FastApiService.php → Communicates with FastAPI
- chatbot.blade.php → User interface


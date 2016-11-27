#!/usr/bin/env sh
python manage.py collectstatic --noinput
gunicorn src.wsgi -w 2 -b 0.0.0.0:8000

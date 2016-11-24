FROM python:alpine
MAINTAINER Kenan Rhoton
WORKDIR /data/web
COPY requirements.txt .

RUN pip install --upgrade pip
RUN pip install -r requirements.txt

COPY src/* .
CMD python3 manage.py runserver 0.0.0.0:8000

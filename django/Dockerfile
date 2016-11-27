FROM python:alpine
MAINTAINER Kenan Rhoton
WORKDIR /data/src
COPY requirements.txt .

RUN pip install --upgrade pip
RUN pip install -r requirements.txt
CMD /data/src/startup.sh

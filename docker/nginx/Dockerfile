FROM nginx:1.19

ARG UID
EXPOSE $UID

RUN adduser -u 1001 --disabled-password --gecos "" appuser

COPY default.conf /etc/nginx/conf.d/
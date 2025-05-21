FROM nginx:1.21

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www

CMD ["nginx", "-g", "daemon off;"]

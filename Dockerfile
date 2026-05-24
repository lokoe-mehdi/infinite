FROM php:8.3-cli-alpine

WORKDIR /app
COPY index.php .

EXPOSE 8000

# index.php sert de routeur : toutes les URLs passent par lui
CMD ["php", "-S", "0.0.0.0:8000", "index.php"]

FROM --platform=linux/arm64 tjdft/laravel:local

USER root
RUN apt update && apt install -y php8.1-mongodb
USER appuser

COPY --chown=appuser:appuser . .
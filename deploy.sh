#!/usr/bin/env bash

ssh -t mathieu@178.62.71.43 'cd final-year-project && git pull && docker-compose up --build -d'

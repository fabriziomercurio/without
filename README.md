Without – Backend PHP Application

Backend application developed in PHP with MySQL and completaly containerized in Docker  
The project born for learn modern architecture, create an environment for reproducible develop and provide a modular backend

-- Technologies used

- PHP 8
- Apache 
- MySQL 
- Docker
- phpMyAdmin
- Redis 

-- Starting the project

1. Clone repository

git clone https://github.com/fabriziomercurio/without.git 
cd without

2. Start containers

docker-compose up --build

3. Services available

| Service        | URL                     |
|-----------------|-------------------------|
| Backend PHP     | http://localhost:8080   |
| phpMyAdmin      | http://localhost:8081   |
| MySQL           | port 3360              |

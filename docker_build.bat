docker build -t rest_2020 . --no-cache 
docker run -dit -p 8080-9000:8080 --name rest_2020 -v C:/Users/RobDaniel/git/REST_bm:/var/www/html --env-file C:/Users/RobDaniel/git/REST_bm/dev_env.list rest_2020

FROM node:25-slim

COPY . /home/node/app

WORKDIR /home/node/app

RUN ["npm", "install"]

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]
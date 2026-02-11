FROM node:20-alpine

WORKDIR /app

RUN npm install -g create-vite@latest

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]
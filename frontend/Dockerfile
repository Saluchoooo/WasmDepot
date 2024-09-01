# Utilitza una imatge base de Node.js
FROM node:latest

# Configura el directori de treball
WORKDIR /app

# Copia els fitxers package.json i package-lock.json al directori de treball
COPY package*.json ./

# Instal·la les dependències del projecte, incloent Vuetify
RUN npm install vuetify

# Copia la resta del codi del projecte Vue.js al contenidor
COPY . .

# Instal·la les dependències restants amb npm
RUN npm install

# Exposa el port 8080
EXPOSE 8080

# Comanda per iniciar l'aplicació de desenvolupament
CMD npm run serve

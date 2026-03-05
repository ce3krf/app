import mysql.connector
import googlemaps
import random
import time

# Configura tu conexión a la base de datos
db = mysql.connector.connect(
    host="mysql.comunidad.city",  # Cambia por tu host
    user="fulltrust",       # Cambia por tu usuario
    password="dsi10349",  # Cambia por tu contraseña
    database="fulltrust_proyectosjuanfernandez"  # Cambia por tu base de datos
)

# Inicializa el cliente de Google Maps con tu API Key
gmaps = googlemaps.Client(key='AIzaSyB_ZeYfRT2kPXZRomzAfmgI8AJXBppzB60')

# Define el área de Juan Bautista en la Isla Robinson Crusoe
lat_range = (-33.645, -33.635)  # Latitud aproximada del pueblo
lng_range = (-78.836, -78.826)  # Longitud aproximada del pueblo

# Obtén los registros de la tabla "proyectos"
cursor = db.cursor(dictionary=True)
cursor.execute("SELECT id FROM proyectos WHERE lat IS NULL OR lng IS NULL")
proyectos = cursor.fetchall()

# Función para generar coordenadas aleatorias dentro del rango
def generar_coordenada_unica(lat_range, lng_range, coords_existentes):
    while True:
        lat = random.uniform(lat_range[0], lat_range[1])
        lng = random.uniform(lng_range[0], lng_range[1])
        coord = (lat, lng)
        if coord not in coords_existentes:
            coords_existentes.add(coord)
            return coord

# Almacenar las coordenadas generadas para asegurar que sean únicas
coords_existentes = set()

# Itera sobre los registros y asigna nuevas coordenadas
for proyecto in proyectos:
    id_proyecto = proyecto['id']
    nueva_lat, nueva_lng = generar_coordenada_unica(lat_range, lng_range, coords_existentes)

    # Actualiza los valores en la base de datos
    cursor.execute("""
        UPDATE proyectos 
        SET lat = %s, lng = %s 
        WHERE id = %s
    """, (nueva_lat, nueva_lng, id_proyecto))
    
    # Confirma la actualización
    db.commit()
    print(f"Actualizado el proyecto {id_proyecto} con lat={nueva_lat}, lng={nueva_lng}")

    # Espera para evitar el límite de la API de Google Maps si haces muchas solicitudes
    time.sleep(1)

# Cierra la conexión
cursor.close()
db.close()

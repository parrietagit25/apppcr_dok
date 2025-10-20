#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script de sincronización de reservas desde BARS Cloud a AppPCR
Ejecutar diariamente para actualizar pantallas de bienvenida
"""

import requests
import datetime
import sys
from requests.auth import HTTPBasicAuth

# ====================================================
# CONFIGURACIÓN
# ====================================================

# API de BARS Cloud (sistema de gestión de rentas)
BARS_API_URL = "https://cq1e.barscloud.com:612/dolPanamaRW/queryapi/apiReservasDateOut.mf"
BARS_USERNAME = "dolPanamaRW"
BARS_PASSWORD = "VfsbJpYp"

# API de AppPCR (ACTUALIZADA)
APPPCR_API_URL = "https://apppcr.net/reservas/insert_reserva.php"

# ====================================================
# FUNCIONES
# ====================================================

def obtener_fecha_hoy():
    """Obtiene la fecha de hoy en formato YYYY-MM-DD"""
    return datetime.date.today().strftime("%Y-%m-%d")


def limpiar_reservas_antiguas():
    """
    Opcional: Limpia las reservas de la tabla antes de insertar nuevas
    Descomenta si quieres limpiar la tabla cada día
    """
    try:
        borrar_url = "https://apppcr.net/reservas/borrar_reserva.php"
        response = requests.post(borrar_url)
        if response.status_code == 200:
            print("✅ Reservas antiguas limpiadas correctamente")
        else:
            print(f"⚠️ Advertencia al limpiar: {response.status_code}")
    except Exception as e:
        print(f"⚠️ Error al limpiar reservas: {e}")


def obtener_reservas_bars(fecha):
    """
    Obtiene las reservas desde la API de BARS Cloud
    
    Args:
        fecha (str): Fecha en formato YYYY-MM-DD
        
    Returns:
        list: Lista de reservas o None si hay error
    """
    try:
        url = f"{BARS_API_URL}?dtsdate={fecha}&dtedate={fecha}"
        
        print(f"📡 Consultando API BARS Cloud para fecha: {fecha}")
        print(f"   URL: {url}")
        
        response = requests.get(
            url,
            auth=HTTPBasicAuth(BARS_USERNAME, BARS_PASSWORD),
            verify=False,  # Desactiva verificación SSL
            timeout=30
        )
        response.raise_for_status()
        
        json_response = response.json()
        
        # Verificar estructura de respuesta
        if "data" not in json_response:
            print("❌ Error: La API no devolvió la clave 'data'")
            print(f"   Respuesta recibida: {json_response}")
            return None
        
        reservas = json_response["data"]
        print(f"✅ Obtenidas {len(reservas)} reservas de BARS Cloud")
        
        return reservas
        
    except requests.exceptions.RequestException as e:
        print(f"❌ Error al consultar BARS Cloud: {e}")
        return None
    except Exception as e:
        print(f"❌ Error inesperado: {e}")
        return None


def enviar_reserva_apppcr(reserva):
    """
    Envía una reserva individual a AppPCR
    
    Args:
        reserva (dict): Datos de la reserva
        
    Returns:
        bool: True si se insertó correctamente, False si hubo error
    """
    try:
        response = requests.post(
            APPPCR_API_URL,
            json=reserva,
            timeout=10
        )
        
        resultado = response.json()
        
        if response.status_code == 200 and resultado.get("success"):
            return True
        else:
            print(f"   ⚠️ Error al insertar: {resultado.get('error', 'Desconocido')}")
            return False
            
    except Exception as e:
        print(f"   ❌ Error al enviar a AppPCR: {e}")
        return False


def sincronizar_reservas():
    """
    Función principal que sincroniza todas las reservas
    """
    print("=" * 60)
    print("🚗 SINCRONIZACIÓN DE RESERVAS AUTOMARKET")
    print("=" * 60)
    
    # Obtener fecha
    fecha_hoy = obtener_fecha_hoy()
    print(f"📅 Fecha: {fecha_hoy}")
    print()
    
    # Opcional: Limpiar reservas antiguas
    # Descomenta la siguiente línea si quieres limpiar antes de insertar
    # limpiar_reservas_antiguas()
    # print()
    
    # Obtener reservas de BARS Cloud
    reservas = obtener_reservas_bars(fecha_hoy)
    
    if not reservas:
        print("❌ No se pudieron obtener reservas. Finalizando.")
        return 1
    
    # Enviar cada reserva a AppPCR
    print(f"\n📤 Enviando {len(reservas)} reservas a AppPCR...")
    print("-" * 60)
    
    exitosas = 0
    fallidas = 0
    
    for i, reserva in enumerate(reservas, 1):
        cliente = reserva.get("customer", "Sin nombre")
        ubicacion = reserva.get("locationcodeout", "N/A")
        
        print(f"{i}/{len(reservas)} - {cliente} ({ubicacion})... ", end="")
        
        if enviar_reserva_apppcr(reserva):
            print("✅")
            exitosas += 1
        else:
            print("❌")
            fallidas += 1
    
    # Resumen
    print("-" * 60)
    print(f"\n📊 RESUMEN:")
    print(f"   ✅ Exitosas: {exitosas}")
    print(f"   ❌ Fallidas:  {fallidas}")
    print(f"   📊 Total:     {len(reservas)}")
    print()
    print("=" * 60)
    
    # Retornar código de salida
    return 0 if fallidas == 0 else 1


# ====================================================
# EJECUCIÓN PRINCIPAL
# ====================================================

if __name__ == "__main__":
    try:
        # Desactiva warnings de SSL inseguro
        import urllib3
        urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
        
        # Ejecutar sincronización
        exit_code = sincronizar_reservas()
        
        sys.exit(exit_code)
        
    except KeyboardInterrupt:
        print("\n\n⚠️ Sincronización cancelada por el usuario")
        sys.exit(1)
    except Exception as e:
        print(f"\n❌ Error fatal: {e}")
        sys.exit(1)


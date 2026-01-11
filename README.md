<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

 ArtWeb – Sistema de Recomendación de Eventos

Aplicación Web desarrollada con Laravel + Inertia + Vue

Integrantes

- Elizabeth Guerrón

- Jean Luc Morales

Link del video:

https://youtu.be/RQVkOjLE7Xk


Descripción general del proyecto

ArtWeb es una aplicación web que permite a los usuarios visualizar eventos culturales en un mapa interactivo. El sistema recomienda eventos de acuerdo con los intereses del usuario y su ubicación geográfica, asignando un nivel de relevancia a cada evento para mostrar primero aquellos que resultan más adecuados para el usuario.

El proyecto fue desarrollado con Laravel en el backend, Inertia.js como puente y Vue.js en el frontend. Como parte del taller formativo, se aplicaron principios SOLID y patrones de diseño para mejorar la estructura, mantenibilidad y escalabilidad del código.

Funcionalidades principales:

- Autenticación de usuarios.

- Gestión de intereses del usuario.

- Creación y visualización de eventos.

- Mapa interactivo con eventos geolocalizados.

- Sistema de recomendación que asigna relevancia a los eventos según:

- Intereses del usuario.

- Coincidencia exacta de categorías.

- Coincidencia por nombre de interés.

- Distancia entre el usuario y el evento.

PROBLEMAS EN EL SISTEMA:

Inicialmente, la lógica de recomendación se encontraba directamente en el controlador EventoController.
El EventoController se encargaba de:

- Consultar los eventos desde la base de datos.

- Calcular la distancia entre el usuario y cada evento.

- Comparar intereses.

- Calcular el puntaje de relevancia.

- Ordenar los resultados.

Esto provocaba que el controlador tuviera demasiadas responsabilidades, volviéndolo difícil de mantener, poco reutilizable y propenso a errores cuando se requerían cambios en la lógica.


PRINCIPIOS SOLID IMPLEMENTADOS:

1. Single Responsibility Principle (SRP)

El principio de Responsabilidad Única establece que una clase debe encargarse de una sola tarea y tener una única razón para cambiar. En el proyecto, este principio se aplicó separando claramente las funciones del sistema: el EventoController se encarga únicamente de manejar las peticiones HTTP y, específicamente en el método actualizarMapa(), solo recibe los datos del usuario y devuelve la respuesta sin realizar cálculos complejos; la clase UserContext tiene como única responsabilidad transportar la información del usuario, como intereses y ubicación, sin contener ninguna lógica de negocio; el EventoRepository se encarga exclusivamente de obtener los eventos desde la base de datos; el EventoRecomendadorService coordina el proceso de cálculo de relevancia de los eventos; y finalmente, las clases de reglas (ReglaDistancia, ReglaInteresExacto y ReglaNombreInteres) se encargan cada una de calcular el puntaje según un criterio específico. Esta separación evita la creación de clases grandes y desordenadas, facilitando el mantenimiento del sistema y mejorando la comprensión del código.

2. Dependency Inversion Principle (DIP)

Principio:
Las clases de alto nivel no deben depender de implementaciones concretas, sino de abstracciones.

Aplicación en el proyecto:

Se creó la interfaz ReglaRelevancia.

Todas las reglas implementan el método:

puntaje(Evento $evento, UserContext $ctx)


EventoRecomendadorService no depende de reglas específicas, sino de la interfaz.

Problema que soluciona:
Permite agregar nuevas reglas de recomendación sin modificar la lógica principal del sistema.

PATRONES DE DISEÑO IMPLEMENTADOS:

1. Repository Pattern
 
Clase:
App\Repositories\EventoRepository

El metodo:
obtenerParaMapa()

Se encarga de encapsular las consultas a la base de datos para obtener los eventos necesarios para el mapa.


Antes las consultas Eloquent estaban directamente en el controlador. Especificamente en EventoController


La aplicacion de este patron de diseño logra que el acceso a datos está centralizado, evitando duplicación y facilitando cambios futuros.

2. Strategy Pattern

Regla aplicada en todas:
ReglaRelevancia

Estrategias implementadas:

- ReglaInteresExacto

- ReglaNombreInteres

- ReglaDistancia

Cada clase implementa su propia forma de calcular el puntaje de relevancia.

Clase que utiliza las estrategias:
EventoRecomendadorService

Este patron evita un método gigante con múltiples condicionales y permite agregar nuevas reglas sin afectar las existentes.

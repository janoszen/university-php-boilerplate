# PHP boilerplate for simple university projects

This codebase is intended for a simple, frameworkless boilerplate for university projects. It uses a small number of 
libraries to do the most essential functions, but nothing that you can't easily replace with your own implementation.

- [Auryn DIC](https://github.com/rdlowrey/auryn)
- [Guzzle HTTP](http://guzzlephp.org/)
- [FastRoute](https://github.com/nikic/FastRoute)
- [Twig](http://twig.sensiolabs.org/)

**Warning!** This is not intended for real-world use, only for getting university projects in PHP done painlessly.

## Caveats

### Dependency injection

This boilerplate will automatically perform dependency injection. If you don't know what it is, google it.

### PSR-7

The PSR-7 standard in PHP defines standard request and response objects. However, these objects are defined as 
*immutable*, so you can't ever change the value of an existing object; you can only create a copy with a new one. If 
 you want to output HTTP headers (such as setting cookies), you should require HTTPResponseContainer for injection, 
 it will give you the ability to override the response.
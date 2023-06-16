## Localisation en Laravel
Localisation en Laravel : Créer un site multilingue  
  
## Créer une application Laravel
`laravel new localisation  `
  
## Travailler avec des fichiers de traduction
Une ancienne approche qui consiste à stocker vos fichiers sous le chemin suivant : resources/lang/{en,fr,ru}/{myfile.php}. (8.x)  
Une nouvelle approche d'avoir des fichiers /lang/{fr.json, en.json} et aussi /lang/{en,fr}/{myfile.php}  
  
## Traductions simples
Ajoutons une nouvelle clé dans config/app.php  
```
'available_locales' => [  
  'English' => 'en',  
  'French' => 'fr',  
  'Arabic' => 'ar',  
],
```  
## Fichiers de traduction
Commençons par ajouter les fichiers de localisation dans le dossier lang.  
Par expl: lang/fr.json  
`{  
  "Welcome to our website": "Bienvenue sur notre site"  
}`  
  
## Changer de Langue dans Laravel
  
### Méthode 1 : Modification des routes (méthode à eviter)
```Route::get('/{lang?}', function ($lang = null) {
    if (isset($lang) && in_array($lang, config('app.available_locales'))) {
        app()->setLocale($lang);
    }
    return view('welcome');
});
```    
### Méthode 2 : Utlisation d'un meiddleware
`php artisan make:middleware Localization  `
  
```namespace App\Http\Middleware;  
  
use Closure;  
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\App;  
  
class Localization  
{  
    /**   
     * Handle an incoming request.  
     *  
     * @param  \Illuminate\Http\Request  $request  
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next  
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse  
     */  
    public function handle(Request $request, Closure $next)  
    {  
        if (session()->has('locale')) {  
            App::setLocale(session('locale'));  
        }  
  
        return $next($request);  
    }  
}
```
Ce middleware demandera à Laravel d'utiliser les paramètres régionaux sélectionnés par l'utilisateur si cette sélection est présente dans la session.  
  
Comme nous avons besoin que cette opération soit exécutée à chaque requête, nous devons également l'ajouter à la pile middleware par défaut app/http/Kernel.php pour le web group middleware :  
  
`App\Http\Middleware\Localization :: class ,  `
  
### Modification des routes  
Ensuite, nous devons ajouter une route pour changer de paramètres régionaux. Nous utilisons une route closure, mais vous pouvez utiliser exactement le même code dans votre   contrôleur si vous le souhaitez  
  
```Route::get('langue/{lang}', function ($lang) {  
    app()->setLocale($lang);  
    session()->put('locale', $lang);  
  
    return redirect()->back();  
}); 
```  
### Créer un switch
Nous devons maintenant créer quelque chose sur lequel l'utilisateur peut cliquer pour changer la langue :  
  
```@foreach($available_locales as $locale_name => $available_locale)  
        @if($available_locale === $current_locale)  
            <span class="ml-2 mr-2 text-gray-700">{{ $locale_name }}</span>  
        @else  
            <a class="ml-1 underline ml-2 mr-2" href="langue/{{ $available_locale }}">  
                <span>{{ $locale_name }}</span>  
            </a>  
        @endif  
    @endforeach 
```
### Ajouter le code à pargater dans le AppServiceProvider 
Ouvrez le fichier app/Providers/AppServiceProvider.php et ajoutez le code à partager lors de la composition de notre sélecteur de langue. Plus précisément, nous partagerons les paramètres régionaux actuels accessibles en tant que {{ $current_locale }}.  

```public function boot()  
    {  
        view()->composer('components.language_switcher', function ($view) {  
            $view->with('current_locale', app()->getLocale());  
            $view->with('available_locales', config('app.available_locales'));  
        });  
    }
```

# Standardisation des Développements Backend

Cette standardisation assure une structure **claire**, **maintenable** et **cohérente** pour l’ensemble du code backend de l’application MyPetly. Elle s’applique à **tous** les contrôleurs, services, requêtes, ressources et autres fichiers associés.

## 1. Structure générale des méthodes

Chaque méthode exposée par un contrôleur **doit** respecter les règles suivantes :

1. **Requêtes validées**  
   - Créer une *classe de requête* dédiée pour la validation des entrées.  
   - Aucune logique métier ne s’exécute avant la validation complète.

2. **Ressources**  
   - Utiliser une *classe de ressource* pour formater la réponse JSON.  
   - Garantir un format de réponse uniforme.

3. **Gestion des exceptions**  
   - Encapsuler la logique métier dans un bloc `try … catch`.  
   - Renvoyer des erreurs standardisées.

4. **Réponses normalisées**  
   - Utiliser systématiquement `ApiService::response()` avec le code HTTP approprié.

5. **Documentation Swagger**  
   - Ajouter des annotations complètes : description, paramètres, exemples de requête/réponse.

## 2. Contrôleurs

- Responsables **uniquement** de recevoir la requête, d’appeler le service et de renvoyer la réponse.  
- **Aucune** logique métier dans le contrôleur.

## 3. Services

- Contiennent **toute** la logique métier.  
- Testables indépendamment (tests unitaires & fonctionnels).

## 4. Requêtes (Request)

- Une classe de requête **par action** nécessitant une validation.  
- Préfixes : `Store`, `Update` selon le contexte (ex. `StoreUserRequest`).

## 5. Ressources

- Une ressource retourne un JSON homogène et versionnable.  
- Sert de point unique pour transformer les modèles en réponse API.

## 6. Gestion des erreurs

```php
try {
    $data = $service->execute($validated);
    return ApiService::response([
        'message' => 'Opération réussie',
        'data'    => $data
    ], 200);
} catch (\Throwable $e) {
    report($e); // log interne
    return ApiService::response([
        'message' => 'Une erreur est survenue',
        'error'   => config('app.debug') ? $e->getMessage() : null
    ], 500);
}
```

## 7. Documentation Swagger

Pour **chaque** méthode publique :

- `@OA\Get`, `@OA\Post`, …  
- Description courte et précise de l’endpoint.  
- Paramètres (`path`, `query`, `body`) listés et typés.  
- Réponses (`200`, `400`, `401`, `404`, `500`) avec exemples JSON.

## 8. Réponses normalisées

```php
return ApiService::response([
    'message' => 'Opération réussie',
    'data'    => $data
], 200);
```

## 9. Convention de nommage

| Élément                       | Convention                     | Exemple                |
|-------------------------------|--------------------------------|------------------------|
| Contrôleur                    | Singulier + `Controller`       | `UserController`       |
| Service                       | Suffixe `Service`              | `UserService`          |
| Classe de requête (create)    | `Store` + Entité + `Request`   | `StoreUserRequest`     |
| Classe de requête (update)    | `Update` + Entité + `Request`  | `UpdateUserRequest`    |
| Ressource                     | Suffixe `Resource`             | `UserResource`         |

## 10. Points à vérifier

- ✔️ Toutes les classes sont correctement liées (contrôleur ⇆ service ⇆ requête ⇆ ressource ⇆ modèle).  
- ✔️ Tests unitaires et fonctionnels passants.  
- ✔️ Swagger se génère sans erreur.  
- ✔️ Réponses JSON conformes au schéma.  
- ✔️ Logique métier *uniquement* dans les services.

---

*Version : 1.0 – 08/06/2025*  
*Auteur : Équipe Backend MyPetly*

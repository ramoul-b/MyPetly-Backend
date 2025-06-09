# Scénario de test de `ProviderPolicy`

Ce document décrit un scénario simple pour vérifier le comportement de la `ProviderPolicy` située dans `app/Policies/ProviderPolicy.php`.

## Préparation des données

1. Créer un objet `Provider` (par exemple avec l'id `1`).
2. Créer trois utilisateurs :
   - **Admin** : possède les permissions `view_any_provider`, `edit_any_provider`, `delete_any_provider` et `create_provider`.
   - **Owner** : possède les permissions `view_own_provider`, `edit_own_provider`, `delete_own_provider` et `create_provider` et son `id` correspond à celui du `Provider` créé.
   - **Guest** : ne possède aucune de ces permissions.

## Cas de test

1. **Affichage**
   - `Admin` peut appeler `view` sur n'importe quel provider → **vrai**.
   - `Owner` peut appeler `view` sur son propre provider → **vrai**.
   - `Owner` ne peut pas `view` un autre provider sans la permission globale → **faux**.
   - `Guest` ne peut pas `view` → **faux**.
2. **Création**
   - `Admin` ou `Owner` peuvent appeler `create` → **vrai**.
   - `Guest` → **faux**.
3. **Mise à jour**
   - `Admin` peut mettre à jour n'importe quel provider → **vrai**.
   - `Owner` peut mettre à jour son provider → **vrai**.
   - `Owner` ne peut pas mettre à jour un autre provider → **faux**.
   - `Guest` → **faux**.
4. **Suppression**
   - `Admin` peut supprimer n'importe quel provider → **vrai**.
   - `Owner` peut supprimer son provider → **vrai**.
   - `Owner` ne peut pas supprimer un autre provider → **faux**.
   - `Guest` → **faux**.

## Mise en œuvre

Chaque cas peut être implémenté dans un test PHPUnit en utilisant `Gate::forUser($user)->allows('action', $provider)` ou directement la policy :

```php
$policy = new ProviderPolicy();
$result = $policy->view($user, $provider);
```

Les assertions vérifieront que la valeur retournée correspond aux attentes décrites ci-dessus.

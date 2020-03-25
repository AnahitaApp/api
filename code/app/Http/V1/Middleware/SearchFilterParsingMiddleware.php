<?php
declare(strict_types=1);

namespace App\Http\V1\Middleware;

use Closure;
use Illuminate\Support\Str;
use App\Exceptions\ValidationException;

/**
 * Class SearchFilterParsingMiddleware
 * @package App\Http\V1\Middleware
 */
class SearchFilterParsingMiddleware
{
    /**
     * Add a parsed filter and search to the request as the `refine` variable
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws ValidationException
     */
    public function handle($request, Closure $next)
    {
        $filter = [];
        $search = [];

        if ($filters = $request->query('filter')) {
            if (is_array($filters)) {
                foreach ($filters as $key => $value) {
                    $filter = $this->processQueryEntry($filter, $key, $value);
                }
            }
        }
        if ($filter) {
            $request->query->set('cleaned_filter', $filter);
        }


        if ($searches = $request->query('search')) {
            if (is_array($searches)) {
                foreach ($searches as $key => $value) {
                    $search = $this->processQueryEntry($search, $key, $value);
                }
            }
        }

        if ($search) {
            $request->query->set('cleaned_search', $search);
        }

        return $next($request);
    }

    /**
     * Creates a search term, and returns the array with the new search term added
     *
     * @param $currentQuery
     * @param $key
     * @param $value
     * @return array
     * @throws ValidationException
     */
    private function processQueryEntry($currentQuery, $key, $value): array
    {
        $parts = explode(',', $value, 2);

        if (count($parts) == 1) {
            switch ($parts[0]) {
                case 'notnull':
                    $searchType = '<>';
                    $searchTerm = '';
                    break;

                case 'null':
                    $searchType = 'IS NULL';
                    $searchTerm = null;
                    break;

                default:
                    $searchType = '=';
                    $searchTerm = urldecode($parts[0]);
                    break;
            }

            $currentQuery[] = [$key, $searchType, $searchTerm];
        } else {

            $searchTerm = $parts[1];

            switch ($parts[0]) {
                case 'gt':
                    $searchType = '>';
                    break;
                case 'gte':
                    $searchType = '>=';
                    break;
                case 'lt':
                    $searchType = '<';
                    break;
                case 'lte':
                    $searchType = '<=';
                    break;
                case 'eq':
                    $searchType = '=';
                    break;
                case 'ne':
                    $searchType = '!=';
                    break;

                case 'in':
                    $searchType = 'in';
                    $searchTerm = explode(',', $searchTerm);
                    break;
                case 'notin':
                    $searchType = 'not in';
                    $searchTerm = explode(',', $searchTerm);
                    break;

                case 'like':
                    $searchType = 'like';
                    if (Str::startsWith($searchTerm, '*')) $searchTerm = '%' . substr($searchTerm, 1);
                    if (Str::endsWith($searchTerm, '*')) $searchTerm = substr($searchTerm, 0, -1) . '%';
                    break;

                case 'between':
                    $searchType = 'between';
                    $searchTerm = explode(',', $searchTerm);
                    break;

                default:
                    throw new ValidationException(sprintf('Search term [%s] is has an invalid qualifier [%s]', $key, $parts[0]));
            }

            if ($searchType == 'between') { // I wish I could figure out how to get the 'between' operator to work - but it has a hardcoded $type = 'basic' in the where()
                $currentQuery[] = [$key, '>=', urldecode($searchTerm[0])];
                $currentQuery[] = [$key, '<=', urldecode($searchTerm[1])];
            } elseif ($searchType == 'like') {
                $currentQuery[] = [$key, $searchType, $searchTerm];
            } else {
                $currentQuery[] = [$key, $searchType, is_array($searchTerm) ? array_map('urldecode', $searchTerm) : urldecode($searchTerm)];
            }
        }

        return $currentQuery;
    }
}

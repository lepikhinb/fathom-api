<?php

namespace Based\Fathom\Endpoints;

use Based\Fathom\Api;
use Based\Fathom\Collections\SiteCollection;
use Based\Fathom\Enums\Sharing;
use Based\Fathom\Exceptions\IncorrectValueException;
use Based\Fathom\Exceptions\MissingValueException;
use Based\Fathom\Models\Site;

class SiteEndpoint
{
    protected ?string $cursor = null;

    public function __construct(
        protected Api $api
    ) {
    }

    /**
     * Return a list of all sites
     *
     * @param  int  $limit  A limit on the number of objects to be returned, between 1 and 100.
     * @param  bool  $next  Paginate request
     * @return SiteCollection|Site[]
     *
     * @throws \Based\Fathom\Exceptions\AuthenticationException
     * @throws \Exception
     */
    public function get(int $limit = 10, bool $next = false): SiteCollection
    {
        $data = $this->api->get('sites', [
            'starting_after' => $next ? $this->cursor : null,
            'limit' => $limit,
        ])->json('data');

        $collection = new SiteCollection($data);
        $this->cursor = $collection->last()?->id;

        return $collection;
    }

    /**
     * Return a single site
     *
     * @param  string  $siteid  The ID of the site you wish to load. This is the same string you use in the tracking code.
     * @return \Based\Fathom\Models\Site
     *
     * @throws \Based\Fathom\Exceptions\AuthenticationException
     * @throws \Exception
     */
    public function getSite(string $siteid): Site
    {
        $data = $this->api->get("sites/$siteid")->json();

        return new Site(...$data);
    }

    /**
     * Create a site
     *
     * @param  string  $name  The name of the website. Any string (up to 255 characters) is acceptable, and it doesn't have to match the website URL
     * @param  null|string  $sharing  The sharing configuration. Supported values are: `none`, `private` or `public`. Default: `none`
     * @param  null|string  $password  When sharing is set to private, you must also send a password to access the site with.
     * @return \Based\Fathom\Models\Site
     *
     * @throws \Based\Fathom\Exceptions\MissingValueException
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     * @throws \Based\Fathom\Exceptions\AuthenticationException
     * @throws \Exception
     */
    public function create(string $name, ?string $sharing = null, ?string $password = null): Site
    {
        if ($sharing === Sharing::PRIVATE && ! $password) {
            throw new MissingValueException('You must specify a password for a private shared site');
        }

        if ($sharing && ! in_array($sharing, Sharing::values())) {
            throw new IncorrectValueException('Incorrect sharing option specified');
        }

        $data = $this->api->post('sites', [
            'name' => $name,
            'sharing' => $sharing,
            'sharing_password' => $password,
        ])->json();

        return new Site(...$data);
    }

    /**
     * Update a site
     *
     * @param  string $siteid  The ID of the site you wish to update. This is the same string you use in the tracking code.
     * @param  null|string  $name  The name of the website. Any string (up to 255 characters) is acceptable, and it doesn't have to match the website URL
     * @param  null|string  $sharing  The sharing configuration. Supported values are: `none`, `private` or `public`. Default: `none`
     * @param  null|string  $password  When sharing is set to private, you must also send a password to access the site with.
     * @return \Based\Fathom\Models\Site
     *
     * @throws \Based\Fathom\Exceptions\MissingValueException
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     * @throws \Based\Fathom\Exceptions\AuthenticationException
     * @throws \Exception
     */
    public function update(string $siteid, ?string $name = null, ?string $sharing = null, ?string $password = null): Site
    {
        if ($sharing === Sharing::PRIVATE && ! $password) {
            throw new MissingValueException('You must specify a password for a private shared site');
        }

        if ($sharing && ! in_array($sharing, Sharing::values())) {
            throw new IncorrectValueException('Incorrect sharing option specified');
        }

        $data = $this->api->post("sites/$siteid", [
            'name' => $name,
            'sharing' => $sharing,
            'sharing_password' => $password,
        ])->json();

        return new Site(...$data);
    }

    /**
     * Wipe all pageviews & event completions from a website. This would typically we used when you want to completely reset statistics or right before you launch a website (to remove test data).
     *
     * @param  string  $siteid  The ID of the site you wish to wipe. This is the same string you use in the tracking code.
     * @return void
     *
     * @throws \Based\Fathom\Exceptions\AuthenticationException
     * @throws \Exception
     */
    public function wipe(string $siteid): void
    {
        $this->api->delete("sites/$siteid/data");
    }

    /**
     * Delete a site (careful, you can't undo this)
     *
     * @param  string  $siteid  The ID of the site you wish to delete. This is the same string you use in the tracking code.
     * @return void
     *
     * @throws \Based\Fathom\Exceptions\AuthenticationException
     * @throws \Exception
     */
    public function delete(string $siteid): void
    {
        $this->api->delete("sites/$siteid");
    }
}

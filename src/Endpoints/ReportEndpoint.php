<?php

namespace Based\Fathom\Endpoints;

use Based\Fathom\Api;
use Based\Fathom\Enums\Aggregate;
use Based\Fathom\Enums\DateInterval;
use Based\Fathom\Enums\Entity;
use Based\Fathom\Enums\FilterOperator;
use Based\Fathom\Enums\FilterProperty;
use Based\Fathom\Enums\Group;
use Based\Fathom\Exceptions\IncorrectValueException;
use Based\Fathom\Exceptions\MissingValueException;
use Based\Fathom\Models\Event;
use Based\Fathom\Models\Site;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Support\Arr;

class ReportEndpoint
{
    protected string $entity;
    protected string $entityId;
    protected string $aggregates;
    protected ?string $dateGrouping = null;
    protected ?string $fieldGrouping = null;
    protected ?string $timezone = null;
    protected ?string $dateFrom = null;
    protected ?string $dateTo = null;
    protected ?string $sortBy = null;
    protected array $filters = [];
    protected ?int $limit = null;

    public function __construct(
        protected Api $api,
        null | string | Site | Event $entity = null,
        ?string $entityId = null
    ) {
        if ($entity) {
            $this->for($entity, $entityId);
        }
    }

    /**
     * Specify the entity you want to report on
     *
     * @param  string|\Based\Fathom\Models\Site|\Based\Fathom\Models\Event  $entity  The entity you want to report on. Supported values: `pageview` and `event`.
     * @param  null|string  $entityId  The ID of the entity that you want to report on
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     * @throws \Based\Fathom\Exceptions\MissingValueException
     */
    public function for(string | Site | Event $entity, ?string $entityId = null): self
    {
        if ($entity instanceof Site) {
            $this->entity = Entity::PAGEVIEW;
            $this->entityId = $entity->id;

            return $this;
        }

        if ($entity instanceof Event) {
            $this->entity = Entity::EVENT;
            $this->entityId = $entity->id;

            return $this;
        }

        if (! in_array($entity, Entity::values())) {
            throw new IncorrectValueException('Incorrect entity type specified');
        }

        if (! $entityId) {
            throw new MissingValueException('Entity ID is missing');
        }

        $this->entity = $entity;
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Set SUM aggregates you need to get
     *
     * @param  string|array  $values  The SUM aggregates you wish to include. Supported values: `visits`, `uniques`, `pageviews`, `avg_duration` and `bounce_rate`
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     */
    public function aggregate(string | array $values): self
    {
        $values = Arr::wrap($values);

        if (count(array_diff($values, Aggregate::values()))) {
            throw new IncorrectValueException('Incorrect aggregate column specified');
        }

        $this->aggregates = implode(',', $values);

        return $this;
    }

    /**
     * Group by date interval
     *
     * @param  string  $interval  Date interval. Supported values: `hour`, `day`, `month` and `year`.
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     */
    public function interval(string $interval): self
    {
        if (! in_array($interval, DateInterval::values())) {
            throw new IncorrectValueException('Incorrect date interval specified');
        }

        $this->dateGrouping = $interval;

        return $this;
    }

    /**
     * Group by hour
     *
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     */
    public function hourly(): self
    {
        return $this->interval(DateInterval::HOUR);
    }

    /**
     * Group by day
     *
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     */
    public function daily(): self
    {
        return $this->interval(DateInterval::DAY);
    }

    /**
     * Group by month
     *
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     */
    public function monthly(): self
    {
        return $this->interval(DateInterval::MONTH);
    }

    /**
     * Group by year
     *
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     */
    public function yearly(): self
    {
        return $this->interval(DateInterval::YEAR);
    }

    /**
     * Group by a field
     *
     * @param  string  $field  The field you want to group by
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     */
    public function groupBy(string $field): self
    {
        if (! in_array($field, Group::values())) {
            throw new IncorrectValueException('Incorrect group field specified');
        }

        $this->fieldGrouping = $field;

        return $this;
    }

    /**
     * Set the timezone
     *
     * @param  string  $value  Timezone. The timezone should be a TZ database name.
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     */
    public function timezone(string $value): self
    {
        $this->timezone = $value;

        return $this;
    }

    /**
     * Filter by date FROM
     *
     * @param  string|\DateTimeInterface  $date
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function from(string | DateTimeInterface $date): self
    {
        $this->dateFrom = Carbon::parse($date)->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * Filter by date TO
     *
     * @param  string|\DateTimeInterface  $date
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function to(string | DateTimeInterface $date): self
    {
        $this->dateTo = Carbon::parse($date)->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * Filter by date
     *
     * @param  string|\DateTimeInterface  $from
     * @param  string|\DateTimeInterface  $to
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Carbon\Exceptions\InvalidFormatException
     */
    public function between(string | DateTimeInterface $from, string | DateTimeInterface $to): self
    {
        return $this->from($from)->to($to);
    }

    /**
     * Add a filter to the query
     *
     * @param  string  $property  Filter property
     * @param  string  $operator  Filter operator. Supported values: `is`, `=`, `!=`, `<>`, `is not`,
     * @param  string|null  $value
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     */
    public function where(string $property, string $operator, string $value = null): self
    {
        if (! in_array($property, FilterProperty::values())) {
            throw new IncorrectValueException('Incorrect filter property specified');
        }

        $operator = match ($operator) {
            'is' => FilterOperator::IS,
            '=' => FilterOperator::IS,
            'is not' => FilterOperator::NOT,
            'not' => FilterOperator::NOT,
            '!=' => FilterOperator::NOT,
            '<>' => FilterOperator::NOT,
            default => $operator
        };

        if (! in_array($operator, FilterOperator::values())) {
            throw new IncorrectValueException('Incorrect filter operator specified');
        }

        $this->filters[] = [
            'property' => $property,
            'operator' => $operator,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * Order results by a field
     *
     * @param  string  $field
     * @param  bool  $descending
     * @return \Based\Fathom\Endpoints\ReportEndpoint
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     */
    public function orderBy(string $field, bool $descending = false): self
    {
        if (! in_array($field, ['timestamp'] + Group::values() + Aggregate::values())) {
            throw new IncorrectValueException('Incorrect sort field specified');
        }

        $this->sortBy = $field . ':' . ($descending ? 'desc' : 'asc');

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get results
     *
     * @param  null|string|\Based\Fathom\Models\Site|\Based\Fathom\Models\Event  $entity
     * @param  null|string  $entityId
     * @param  null|string|array  $aggregates
     * @return array
     *
     * @throws \Based\Fathom\Exceptions\IncorrectValueException
     * @throws \Based\Fathom\Exceptions\MissingValueException
     * @throws \Based\Fathom\Exceptions\AuthenticationException
     * @throws \Exception
     */
    public function get(string | Site | Event | null $entity = null, ?string $entityId = null, string | array | null $aggregates = null): array
    {
        if ($entity) {
            $this->for($entity, $entityId);
        }

        if ($aggregates) {
            $this->aggregate($aggregates);
        }

        $this->validate();

        return $this->api->get('aggregations', $this->query())->json();
    }

    /**
     * Dump query
     *
     * @return array
     */
    public function query(): array
    {
        return collect([
            'entity' => $this->entity,
            'entity_id' => $this->entityId,
            'aggregates' => $this->aggregates,
            'date_grouping' => $this->dateGrouping,
            'field_grouping' => $this->fieldGrouping,
            'timezone' => $this->timezone,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'sort_by' => $this->sortBy,
            'filters' => $this->filters,
            'limit' => $this->limit,
        ])
            ->whereNotNull()
            ->toArray();
    }

    /**
     * Validate request
     *
     * @return void
     *
     * @throws \Based\Fathom\Exceptions\MissingValueException
     */
    public function validate(): void
    {
        if (! isset($this->entity)) {
            throw new MissingValueException('Entity type is missing');
        }

        if (! isset($this->entityId)) {
            throw new MissingValueException('Entity ID is missing');
        }

        if (! isset($this->aggregates)) {
            throw new MissingValueException('Aggregate field is missing');
        }
    }
}

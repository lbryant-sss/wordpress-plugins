<?php

declare(strict_types=1);

namespace WpRollback\Free\Dependencies\StellarWP\AdminNotices;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use WpRollback\Free\Dependencies\StellarWP\AdminNotices\ValueObjects\NoticeLocation;
use WpRollback\Free\Dependencies\StellarWP\AdminNotices\ValueObjects\NoticeUrgency;
use WpRollback\Free\Dependencies\StellarWP\AdminNotices\ValueObjects\ScreenCondition;
use WpRollback\Free\Dependencies\StellarWP\AdminNotices\ValueObjects\Script;
use WpRollback\Free\Dependencies\StellarWP\AdminNotices\ValueObjects\Style;
use WpRollback\Free\Dependencies\StellarWP\AdminNotices\ValueObjects\UserCapability;

class AdminNotice
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string|callable
     */
    protected $renderTextOrCallback;

    /**
     * @var UserCapability[]
     */
    protected $userCapabilities;

    /**
     * @var DateTimeInterface
     */
    protected $afterDate;

    /**
     * @var DateTimeInterface
     */
    protected $untilDate;

    /**
     * @var callable
     */
    protected $whenCallback;

    /**
     * @var ScreenCondition[]
     */
    protected $onConditions;

    /**
     * @var bool
     */
    protected $autoParagraph = false;

    /**
     * @var NoticeUrgency
     */
    protected $urgency;

    /**
     * @var bool
     */
    protected $alternateStyles = false;

    /**
     * @var bool Indicates that the notice is customized and not the standard WordPress notice
     */
    protected $custom = false;

    /**
     * @var bool
     */
    protected $dismissible = false;

    /**
     * @var NoticeLocation|null
     */
    protected $location;

    /**
     * @var Script
     */
    protected $scriptToEnqueue;

    /**
     * @var Style
     */
    protected $styleToEnqueue;

    /**
     * @since 1.0.0
     *
     * @param string|callable $renderTextOrCallback
     */
    public function __construct(string $id, $renderTextOrCallback)
    {
        if (!is_string($renderTextOrCallback) && !is_callable($renderTextOrCallback)) {
            throw new InvalidArgumentException('The renderTextOrCallback argument must be a string or a callable');
        }

        $this->id = $id;
        $this->renderTextOrCallback = $renderTextOrCallback;
        $this->urgency = NoticeUrgency::info();
        $this->location = NoticeLocation::standard();
    }

    /**
     * Limits the notice to display based on the capabilities of the current user
     *
     * @since 1.0.0
     *
     * @param string|array ...$capabilities String or array of arguments compatible with current_user_can()
     *
     * @return $this
     */
    public function ifUserCan(...$capabilities): self
    {
        $this->userCapabilities = [];

        // Validate and store the capabilities
        foreach ($capabilities as $capability) {
            if (empty($capability)) {
                throw new InvalidArgumentException('Capability must be a non-empty string or array');
            } elseif (is_string($capability)) {
                $this->userCapabilities[] = new UserCapability($capability);
            } elseif (is_array($capability) && is_string($capability[0])) {
                $this->userCapabilities[] = new UserCapability($capability[0], array_slice($capability, 1));
            } elseif ($capability instanceof UserCapability) {
                $this->userCapabilities[] = $capability;
            } else {
                throw new InvalidArgumentException(
                    'Invalid capability type. Must be string or array of arguments compatible with current_user_can()'
                );
            }
        }

        return $this;
    }

    /**
     * Limits the notice to display after a specific date
     *
     * @since 1.0.0
     *
     * @param $date DateTimeInterface|string|int if a string then it will be considered UTC
     *
     * @return $this
     * @throws Exception If the date is not a valid DateTimeInterface or string
     */
    public function after($date): self
    {
        $this->afterDate = $this->parseDate($date);

        return $this;
    }

    /**
     * Limits the notice to display until a specific date
     *
     * @since 1.0.0
     *
     * @param $date DateTimeInterface|string|int if a string then it will be considered UTC
     *
     * @throws Exception If the date is not a valid DateTimeInterface or string
     */
    public function until($date): self
    {
        $this->untilDate = $this->parseDate($date);

        return $this;
    }

    /**
     * Limits the notice to a specific date range
     *
     * @param $after DateTimeInterface|string if a string then it will be considered UTC
     * @param $until DateTimeInterface|string if a string then it will be considered UTC
     *
     * @throws Exception If the date is not a valid DateTimeInterface or string
     */
    public function between($after, $until): self
    {
        return $this->after($after)->until($until);
    }

    /**
     * Provide a callback which returns a boolean to determine if the notice should be displayed
     *
     * @since 1.0.0
     */
    public function when(callable $callback): self
    {
        $this->whenCallback = $callback;

        return $this;
    }

    /**
     * Limits the notice to display on specific screens
     *
     * @since 1.0.0
     *
     * @param array|string|ScreenCondition $on
     */
    public function on(...$on): self
    {
        foreach ($on as $condition) {
            $this->onConditions[] = $condition instanceof ScreenCondition ? $condition : new ScreenCondition(
                $condition
            );
        }

        return $this;
    }

    /**
     * Automatically applies paragraph tags to the notice content
     *
     * @since 1.0.0
     */
    public function autoParagraph(bool $auto = true): self
    {
        $this->autoParagraph = $auto;

        return $this;
    }

    /**
     * Disables automatic paragraph tagging
     *
     * @since 1.0.0
     */
    public function withoutAutoParagraph(): self
    {
        $this->autoParagraph = false;

        return $this;
    }

    /**
     * Sets the urgency of the notice, used when the notice is displayed in the standard wrapper
     *
     * @since 1.0.0
     *
     * @param $urgency string|NoticeUrgency
     */
    public function urgency($urgency): self
    {
        $this->urgency = $urgency instanceof NoticeUrgency ? $urgency : new NoticeUrgency($urgency);

        return $this;
    }

    /**
     * Alias for setting the urgency to info
     *
     * @since 1.1.0
     */
    public function asInfo(): self
    {
        return $this->urgency(NoticeUrgency::info());
    }

    /**
     * Alias for setting the urgency to success
     *
     * @since 1.1.0
     */
    public function asSuccess(): self
    {
        return $this->urgency(NoticeUrgency::success());
    }

    /**
     * Alias for setting the urgency to warning
     *
     * @since 1.1.0
     */
    public function asWarning(): self
    {
        return $this->urgency(NoticeUrgency::warning());
    }

    /**
     * Alias for setting the urgency to error
     *
     * @since 1.1.0
     */
    public function asError(): self
    {
        return $this->urgency(NoticeUrgency::error());
    }

    /**
     * Uses the alternate WP notice styles
     *
     * @since 1.2.0
     */
    public function alternateStyles(bool $altStyle = true): self
    {
        $this->alternateStyles = $altStyle;

        return $this;
    }

    /**
     * Uses the standard WP notice styles
     *
     * @since 1.2.0
     */
    public function standardStyles(): self
    {
        $this->alternateStyles = false;

        return $this;
    }

    /**
     * Returns whether the notice uses the alternate WP notice styles
     *
     * @since 1.2.0
     */
    public function usesAlternateStyles(): bool
    {
        return $this->alternateStyles;
    }

    public function custom(bool $custom = true): self
    {
        $this->custom = $custom;

        return $this;
    }

    public function standard(): self
    {
        $this->custom = false;

        return $this;
    }

    /**
     * Sets the notice to be inline
     *
     * @since 2.0.0 removed parameter in favor of new location parameter
     * @since 1.2.0
     */
    public function inline(): self
    {
        $this->location = NoticeLocation::inline();

        return $this;
    }

    /**
     * Prevents the notice from being moved from the place it's rendered
     *
     * @since 2.0.0
     */
    public function inPlace(): self
    {
        $this->location = null;

        return $this;
    }

    public function location($location): self
    {
        $this->location = $location instanceof NoticeLocation ? $location : new NoticeLocation($location);

        return $this;
    }

    public function getLocation(): ?NoticeLocation
    {
        return $this->location;
    }

    /**
     * Sets the notice to be dismissible, usable when the notice is displayed in the standard wrapper
     *
     * @since 1.0.0
     */
    public function dismissible(bool $dismissible = true): self
    {
        $this->dismissible = $dismissible;

        return $this;
    }

    /**
     * Sets the notice to be not dismissible, usable when the notice is displayed in the standard wrapper
     *
     * @since 1.0.0
     */
    public function notDismissible(): self
    {
        $this->dismissible = false;

        return $this;
    }

    /**
     * Returns the notice ID
     *
     * @since 1.0.0
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @since 2.0.0
     */
    public function enqueueScript(
        string $source,
        array $dependencies = [],
        string $version = null,
        array $args = null
    ): self {
        if ($args === null) {
            $args = ['strategy' => 'defer'];
        }

        $this->scriptToEnqueue = new Script($source, $dependencies, $version, $args);

        return $this;
    }

    /**
     * @since 2.0.0
     */
    public function getScriptToEnqueue(): ?Script
    {
        return $this->scriptToEnqueue;
    }

    /**
     * @since 2.0.0
     */
    public function enqueueStylesheet(
        string $source,
        array $dependencies = [],
        string $version = null,
        string $media = 'all'
    ): self {
        $this->styleToEnqueue = new Style($source, $dependencies, $version, $media);

        return $this;
    }

    /**
     * @since 2.0.0
     */
    public function getStyleToEnqueue(): ?Style
    {
        return $this->styleToEnqueue;
    }

    /**
     * Returns the text or callback used to render the notice
     *
     * @since 1.0.0
     *
     * @return callable|string
     */
    public function getRenderTextOrCallback()
    {
        return $this->renderTextOrCallback;
    }

    /**
     * Returns the rendered content of the notice, either by returning the string or executing the callback
     *
     * @since 1.0.0
     */
    public function getRenderedContent(): string
    {
        $render = $this->renderTextOrCallback;

        $content = is_callable($render) ? $render() : $render;

        return $this->autoParagraph ? wpautop($content) : $content;
    }

    /**
     * Returns the user capabilities
     *
     * @since 1.0.0
     *
     * @return UserCapability[]
     */
    public function getUserCapabilities(): ?array
    {
        return $this->userCapabilities;
    }

    /**
     * Returns the date after which the notice should be displayed
     *
     * @since 1.0.0
     */
    public function getAfterDate(): ?DateTimeInterface
    {
        return $this->afterDate;
    }

    /**
     * Returns the date until which the notice should be displayed
     *
     * @since 1.0.0
     */
    public function getUntilDate(): ?DateTimeInterface
    {
        return $this->untilDate;
    }

    /**
     * Returns the callback used to determine if the notice should be displayed
     *
     * @since 1.0.0
     */
    public function getWhenCallback(): ?callable
    {
        return $this->whenCallback;
    }

    /**
     * Returns the screen conditions used to determine if the notice should be displayed
     *
     * @since 1.0.0
     *
     * @return ScreenCondition[]
     */
    public function getOnConditions(): ?array
    {
        return $this->onConditions;
    }

    /**
     * Returns whether the notice content should be automatically wrapped in paragraph tags
     *
     * @since 1.0.0
     */
    public function shouldAutoParagraph(): bool
    {
        return $this->autoParagraph;
    }

    /**
     * Returns the urgency of the notice
     *
     * @since 1.0.0
     */
    public function getUrgency(): NoticeUrgency
    {
        return $this->urgency;
    }

    public function isCustom(): bool
    {
        return $this->custom;
    }

    /**
     * Returns whether the notice is dismissible
     *
     * @since 1.0.0
     */
    public function isDismissible(): bool
    {
        return $this->dismissible;
    }

    /**
     * Parses the date into a DateTimeInterface for the date methods
     *
     * @since 1.0.0
     *
     * @param $date DateTimeInterface|string|int if a string then it will be considered UTC
     *
     * @throws Exception
     */
    private function parseDate($date): DateTimeInterface
    {
        if (is_int($date)) {
            $date = '@' . $date;
        }

        return $date instanceof DateTimeInterface ? $date : new DateTimeImmutable(
            $date,
            new DateTimeZone('UTC')
        );
    }
}

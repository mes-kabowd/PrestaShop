<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductRedirectionSettings;
use RuntimeException;

/**
 * Holds values for product redirect option.
 */
class ProductRedirectOption
{
    /**
     * @var string
     */
    private $redirectType;

    /**
     * @var int
     */
    private $redirectTargetId;

    /**
     * Builds self with a type of no_redirect, automatically filling the only available target id value for this type
     *
     * @todo; separate class
     *
     * @return static
     */
    public static function buildNoRedirect(): self
    {
        return new static(ProductRedirectionSettings::TYPE_NO_REDIRECT, ProductRedirectionSettings::NO_TARGET_VALUE);
    }

    /**
     * Builds self with a provided type and target id
     *
     * @param string $redirectType
     * @param int $redirectTargetId when redirecting to category and target id is 0, then main category will be used
     *                              when redirecting to product, target id is mandatory and cannot be 0
     *
     * @return static
     *
     * @throws RuntimeException
     */
    public static function buildRedirect(string $redirectType, int $redirectTargetId): self
    {
        if ($redirectType === ProductRedirectionSettings::TYPE_NO_REDIRECT) {
            throw new RuntimeException(sprintf('Use "%s"::buildNoRedirect for building "no_redirect" option', static::class));
        }

        return new static($redirectType, $redirectTargetId);
    }

    /**
     * @return string
     */
    public function getRedirectType(): string
    {
        return $this->redirectType;
    }

    /**
     * @return int
     */
    public function getRedirectTargetId(): int
    {
        return $this->redirectTargetId;
    }

    /**
     * Use static factories to initiate this class
     *
     * @param string $redirectType
     * @param int $redirectTargetId
     *
     * @throws ProductConstraintException
     */
    private function __construct(string $redirectType, int $redirectTargetId)
    {
        $this->assertRedirectType($redirectType);
        $this->assertTypeAndIdIntegrity($redirectType, $redirectTargetId);
        $this->redirectType = $redirectType;
        $this->redirectTargetId = $redirectTargetId;
    }

    /**
     * @param string $value
     *
     * @throws ProductConstraintException
     */
    private function assertRedirectType(string $value): void
    {
        if (in_array($value, ProductRedirectionSettings::AVAILABLE_REDIRECT_TYPES)) {
            return;
        }

        throw new ProductConstraintException(
            sprintf(
                'Invalid product redirect type "%s". Allowed types are: "%s"',
                $value,
                implode(',', ProductRedirectionSettings::AVAILABLE_REDIRECT_TYPES)
            ),
            ProductConstraintException::INVALID_REDIRECT_TYPE
        );
    }

    /**
     * @param string $type
     * @param int $id
     *
     * @throws ProductConstraintException
     */
    private function assertTypeAndIdIntegrity(string $type, int $id): void
    {
        $isProductType = $type === ProductRedirectionSettings::TYPE_PRODUCT_TEMPORARY || $type === ProductRedirectionSettings::TYPE_PRODUCT_PERMANENT;

        if ($isProductType) {
            $this->assertProductId($id);

            return;
        }

        $this->assertCategoryId($id);
    }

    /**
     * @param int $id
     *
     * @throws ProductConstraintException
     */
    private function assertProductId(int $id): void
    {
        if ($id <= 0) {
            throw new ProductConstraintException(
                sprintf('Invalid product redirect target id "%s". It is required when redirecting to product', $id),
                ProductConstraintException::INVALID_REDIRECT_TARGET_ID
            );
        }
    }

    /**
     * @param int $id
     *
     * @throws ProductConstraintException
     */
    private function assertCategoryId(int $id): void
    {
        if ($id !== ProductRedirectionSettings::NO_TARGET_VALUE && $id <= 0) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product redirect target id "%s". It must be greater than zero or "%s"',
                    $id,
                    ProductRedirectionSettings::NO_TARGET_VALUE
                ),
                ProductConstraintException::INVALID_REDIRECT_TARGET_ID
            );
        }
    }
}

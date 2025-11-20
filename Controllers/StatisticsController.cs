using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Restoran.Data;
using Restoran.Models;

namespace Restoran.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class StatisticsController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public StatisticsController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Statistics/orders/archived
        [HttpGet("orders/archived")]
        public async Task<ActionResult<IEnumerable<Order>>> GetArchivedOrders()
        {
            return await _context.Orders
                .Include(o => o.Table)
                .Include(o => o.OrderItems)
                    .ThenInclude(oi => oi.MenuItem)
                .Include(o => o.Bill)
                .Where(o => o.Status == OrderStatus.Completed || o.Status == OrderStatus.Cancelled)
                .OrderByDescending(o => o.CompletedAt)
                .ToListAsync();
        }

        // GET: api/Statistics/orders/by-date
        [HttpGet("orders/by-date")]
        public async Task<ActionResult<object>> GetOrdersByDate([FromQuery] DateTime startDate, [FromQuery] DateTime endDate)
        {
            var orders = await _context.Orders
                .Include(o => o.OrderItems)
                    .ThenInclude(oi => oi.MenuItem)
                .Include(o => o.Bill)
                .Where(o => o.CreatedAt >= startDate && o.CreatedAt <= endDate)
                .ToListAsync();

            var stats = new
            {
                TotalOrders = orders.Count,
                CompletedOrders = orders.Count(o => o.Status == OrderStatus.Completed),
                CancelledOrders = orders.Count(o => o.Status == OrderStatus.Cancelled),
                TotalRevenue = orders
                    .Where(o => o.Bill != null && o.Bill.IsPaid)
                    .Sum(o => o.Bill.Total),
                AverageOrderValue = orders
                    .Where(o => o.Bill != null && o.Bill.IsPaid)
                    .Average(o => (decimal?)o.Bill.Total) ?? 0,
                Orders = orders
            };

            return Ok(stats);
        }

        // GET: api/Statistics/revenue/today
        [HttpGet("revenue/today")]
        public async Task<ActionResult<object>> GetTodayRevenue()
        {
            var today = DateTime.Today;
            var tomorrow = today.AddDays(1);

            var bills = await _context.Bills
                .Include(b => b.Order)
                .Where(b => b.IsPaid && b.PaidAt >= today && b.PaidAt < tomorrow)
                .ToListAsync();

            var stats = new
            {
                Date = today.ToString("yyyy-MM-dd"),
                TotalOrders = bills.Count,
                TotalRevenue = bills.Sum(b => b.Total),
                TotalTax = bills.Sum(b => b.Tax),
                TotalSubtotal = bills.Sum(b => b.Subtotal),
                AverageOrderValue = bills.Count > 0 ? bills.Average(b => b.Total) : 0
            };

            return Ok(stats);
        }

        // GET: api/Statistics/popular-items
        [HttpGet("popular-items")]
        public async Task<ActionResult<object>> GetPopularItems([FromQuery] int top = 10)
        {
            var popularItems = await _context.OrderItems
                .Include(oi => oi.MenuItem)
                .GroupBy(oi => new { oi.MenuItemId, oi.MenuItem.Name })
                .Select(g => new
                {
                    MenuItemId = g.Key.MenuItemId,
                    MenuItemName = g.Key.Name,
                    TimesOrdered = g.Sum(oi => oi.Quantity),
                    TotalRevenue = g.Sum(oi => oi.Quantity * oi.PriceAtOrder)
                })
                .OrderByDescending(x => x.TimesOrdered)
                .Take(top)
                .ToListAsync();

            return Ok(popularItems);
        }

        // GET: api/Statistics/tables/usage
        [HttpGet("tables/usage")]
        public async Task<ActionResult<object>> GetTableUsage()
        {
            var tables = await _context.Tables
                .Include(t => t.Orders)
                .ToListAsync();

            var usage = tables.Select(t => new
            {
                TableNumber = t.TableNumber,
                Status = t.Status.ToString(),
                TotalOrders = t.Orders.Count,
                CompletedOrders = t.Orders.Count(o => o.Status == OrderStatus.Completed)
            }).ToList();

            return Ok(usage);
        }

        // GET: api/Statistics/orders/average-time
        [HttpGet("orders/average-time")]
        public async Task<ActionResult<object>> GetAverageOrderTime()
        {
            var completedOrders = await _context.Orders
                .Where(o => o.Status == OrderStatus.Completed && 
                           o.SentToKitchenAt != null && 
                           o.ReadyAt != null &&
                           o.ServedAt != null)
                .ToListAsync();

            if (!completedOrders.Any())
            {
                return Ok(new
                {
                    Message = "No completed orders found",
                    AveragePreparationTime = 0,
                    AverageDeliveryTime = 0,
                    AverageTotalTime = 0
                });
            }

            var avgPrepTime = completedOrders
                .Average(o => (o.ReadyAt.Value - o.SentToKitchenAt.Value).TotalMinutes);

            var avgDeliveryTime = completedOrders
                .Average(o => (o.ServedAt.Value - o.ReadyAt.Value).TotalMinutes);

            var avgTotalTime = completedOrders
                .Average(o => (o.ServedAt.Value - o.SentToKitchenAt.Value).TotalMinutes);

            return Ok(new
            {
                AveragePreparationTime = Math.Round(avgPrepTime, 2),
                AverageDeliveryTime = Math.Round(avgDeliveryTime, 2),
                AverageTotalTime = Math.Round(avgTotalTime, 2),
                Unit = "minutes"
            });
        }
    }
}
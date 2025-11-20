using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Restoran.Data;
using Restoran.Models;

namespace Restoran.Controllers
{
    [Route("api/[controller]")]
    [ApiController]
    public class OrdersController : ControllerBase
    {
        private readonly ApplicationDbContext _context;

        public OrdersController(ApplicationDbContext context)
        {
            _context = context;
        }

        // GET: api/Orders
        [HttpGet]
        public async Task<ActionResult<IEnumerable<Order>>> GetOrders()
        {
            return await _context.Orders
                .Include(o => o.Table)
                .Include(o => o.OrderItems)
                    .ThenInclude(oi => oi.MenuItem)
                .ToListAsync();
        }

        // GET: api/Orders/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Order>> GetOrder(int id)
        {
            var order = await _context.Orders
                .Include(o => o.Table)
                .Include(o => o.OrderItems)
                    .ThenInclude(oi => oi.MenuItem)
                .FirstOrDefaultAsync(o => o.Id == id);

            if (order == null)
            {
                return NotFound();
            }

            return order;
        }

        // PUT: api/Orders/5
        [HttpPut("{id}")]
        public async Task<IActionResult> PutOrder(int id, Order order)
        {
            if (id != order.Id)
            {
                return BadRequest();
            }

            _context.Entry(order).State = EntityState.Modified;

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!OrderExists(id))
                {
                    return NotFound();
                }
                else
                {
                    throw;
                }
            }

            return NoContent();
        }

        // POST: api/Orders
        [HttpPost]
        public async Task<ActionResult<Order>> PostOrder(Order order)
        {
            order.CreatedAt = DateTime.Now;
            order.Status = OrderStatus.New;
            _context.Orders.Add(order);
            await _context.SaveChangesAsync();

            return CreatedAtAction("GetOrder", new { id = order.Id }, order);
        }

        // POST: api/Orders/5/send-to-kitchen
        [HttpPost("{id}/send-to-kitchen")]
        public async Task<IActionResult> SendToKitchen(int id)
        {
            var order = await _context.Orders.FindAsync(id);
            if (order == null)
            {
                return NotFound();
            }

            order.Status = OrderStatus.SentToKitchen;
            order.SentToKitchenAt = DateTime.Now;
            await _context.SaveChangesAsync();

            return Ok(new { message = "Order sent to kitchen", orderId = id });
        }

        // POST: api/Orders/5/mark-ready
        [HttpPost("{id}/mark-ready")]
        public async Task<IActionResult> MarkReady(int id)
        {
            var order = await _context.Orders.FindAsync(id);
            if (order == null)
            {
                return NotFound();
            }

            order.Status = OrderStatus.Ready;
            order.ReadyAt = DateTime.Now;
            await _context.SaveChangesAsync();

            return Ok(new { message = "Order is ready", orderId = id });
        }

        // POST: api/Orders/5/mark-served
        [HttpPost("{id}/mark-served")]
        public async Task<IActionResult> MarkServed(int id)
        {
            var order = await _context.Orders.FindAsync(id);
            if (order == null)
            {
                return NotFound();
            }

            order.Status = OrderStatus.Served;
            order.ServedAt = DateTime.Now;
            await _context.SaveChangesAsync();

            return Ok(new { message = "Order marked as served", orderId = id });
        }

        // POST: api/Orders/5/complete
        [HttpPost("{id}/complete")]
        public async Task<IActionResult> CompleteOrder(int id)
        {
            var order = await _context.Orders.FindAsync(id);
            if (order == null)
            {
                return NotFound();
            }

            order.Status = OrderStatus.Completed;
            order.CompletedAt = DateTime.Now;
            await _context.SaveChangesAsync();

            return Ok(new { message = "Order completed", orderId = id });
        }

        // GET: api/Orders/kitchen
        [HttpGet("kitchen")]
        public async Task<ActionResult<IEnumerable<Order>>> GetKitchenOrders()
        {
            return await _context.Orders
                .Include(o => o.Table)
                .Include(o => o.OrderItems)
                    .ThenInclude(oi => oi.MenuItem)
                .Where(o => o.Status == OrderStatus.SentToKitchen || o.Status == OrderStatus.InProgress)
                .OrderBy(o => o.SentToKitchenAt)
                .ToListAsync();
        }

        // GET: api/Orders/ready
        [HttpGet("ready")]
        public async Task<ActionResult<IEnumerable<Order>>> GetReadyOrders()
        {
            return await _context.Orders
                .Include(o => o.Table)
                .Include(o => o.OrderItems)
                    .ThenInclude(oi => oi.MenuItem)
                .Where(o => o.Status == OrderStatus.Ready)
                .OrderBy(o => o.ReadyAt)
                .ToListAsync();
        }

        // DELETE: api/Orders/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteOrder(int id)
        {
            var order = await _context.Orders.FindAsync(id);
            if (order == null)
            {
                return NotFound();
            }

            _context.Orders.Remove(order);
            await _context.SaveChangesAsync();

            return NoContent();
        }

        private bool OrderExists(int id)
        {
            return _context.Orders.Any(e => e.Id == id);
        }
    }
}
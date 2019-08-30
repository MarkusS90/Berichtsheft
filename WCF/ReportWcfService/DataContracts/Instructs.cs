using System;
using LinqToDB.Mapping;

namespace ReportWcfService
{
    [Table("instructs")]
    public class Instructs
    {
        [Column("apprenticeId", IsPrimaryKey = true)]
        public Int32 ApprenticeId { get; set; }

        [Column("instructorId", IsPrimaryKey = true)]
        public Int32 InstructorId { get; set; }

        public Instructs() { }
    }
}